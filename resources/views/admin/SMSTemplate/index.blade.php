@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

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
                            <h2 class="content-header-title float-left mb-0">Manage SMS Template</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/smstemplate/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span><i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors" id="smstemplate">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Subject</th>
                                    <th>Text Content</th>                                                                   
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($smstemplate as $smstemplate)
                                    <tr>
                                        <td>{{ $smstemplate->id }}</td>                                       
                                        <td>{{ $smstemplate->subject }}</td>                                                                              
                                        <td>{{ $smstemplate->content }}</td>
                                        <td> <a href="javascript:void(0);" onclick="chkupdatestatus({{ $smstemplate->id }}, {{ $smstemplate->status }})">
@if($smstemplate->status == 1) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;|&nbsp;&nbsp;
<a href="{{ url('/admin/smstemplate/'.$smstemplate->id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="javascript:void(0);" onclick="chkdelete({{ $smstemplate->id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>



            </section>
            </div>
        </div>
</div>





    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/ui/data-list-view.js') }}"></script>
<script type="text/javascript">

function chkdelete(id) {

	var chk = confirm('Are you sure! you want to delete this SMS Template?');

	if(chk) {

		window.location = "{{ url('/admin/smstemplate') }}/"+id+"/destroy";

	}

}

function chkupdatestatus(id, status) {

	var statustext = 'Activate';

	if(status == 1) {

		statustext = 'De-Activate';

	}

	var chk = confirm('Are you sure! you want to '+statustext+' this SMS Template ?');

	if(chk) {

		window.location = "{{ url('/admin/smstemplate') }}/"+id+"/"+status+"/updatestatus";

	}

}
</script>	
<script>

$('.dataex-html5-selectors').DataTable( {
	/*dom: 'Bfrtip',
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
	]*/
	 "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});

</script>