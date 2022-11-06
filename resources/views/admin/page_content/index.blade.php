@include('admin.includes.header')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static" data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->

<div class="app-content content" style="background-color:#fff;">
<div class="content-overlay"></div>
<div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:80%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Page Content</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/static_pages/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
							<i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
			<div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors" id = "subscriber">
                            <thead>
                                <tr>                                   
                                    <th>ID</th>   
									<th>Title</th>
									<th>Menu Type</th>									
									<th>Menu Order</th>
									<th>Display Order</th>
									<th>Actions</th> 
                                </tr>
                            </thead>
                            <tbody>
							@foreach($pagecontent as $pagecontent)
                            <tr>
								<td>{{ $pagecontent->Id }}</td>
								<td>{{ $pagecontent->EnTitle }}</td>
								<td>{{ $pagecontent->menu_type }}</td>						
								<td>{{ $pagecontent->parent_id }}</td>
								<td>{{ $pagecontent->display_order }}</td>

								<td><a href="javascript:void(0);" onclick="chkupdatestatus({{ $pagecontent->Id }}, {{ $pagecontent->display_status }})">
                           			 @if($pagecontent->display_status != 0) Activate @else De-Activate @endif 
                            		</a> &nbsp;&nbsp;|&nbsp;&nbsp;<a href="{{ url('/admin/static_pages/'.$pagecontent->Id.'/edit') }}">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            		<a href="javascript:void(0);" onclick="chkdelete({{ $pagecontent->Id }})">Delete</a>
								</td>
							</tr>
                            @endforeach
                            </tbody>
                            </table>
					</div>



            </section>
            </div>
        </div>
</div>

<div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/ui/data-list-view.js') }}"></script>
<script type="text/javascript">
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this Page Content ?');
	if(chk) {
		window.location = "{{ url('/admin/static_pages') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this Page Content ?');
	if(chk) {
		window.location = "{{ url('/admin/static_pages') }}/"+id+"/"+status+"/updatestatus";
	}
}
</script>
<script type="text/javascript">
 $('.dataex-html5-selectors').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [ 0, ':visible' ]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                text: 'JSON',
                action: function ( e, dt, button, config ) {
                    var data = dt.buttons.exportData();

                    $.fn.dataTable.fileSave(
                        new Blob( [ JSON.stringify( data ) ] ),
                        'Export.json'
                    );
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]
    });
	</script>
