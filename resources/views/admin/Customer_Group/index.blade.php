@include('admin.includes.header')
<body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="semi-dark-layout">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.sidemenu')
    <!-- BEGIN: Header-->

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
           
            <div class="content-body">
				<div class="row">
					<div class="col-md-6">
						<div class="breadcrumbs-top">
							<h2 class="content-header-title float-left mb-0">Custpmer Group</h2>

                            <div class="breadcrumb-wrapper col-12">

                                <ol class="breadcrumb">

                                    <li class="breadcrumb-item"><a href="#">Home</a>

                                    </li>

                                    <li class="breadcrumb-item"><a href="#">Custpmer Group</a>

                                    </li>

                                    <li class="breadcrumb-item active">View

                                    </li>

                                </ol>

                            </div>
						</div>
					</div>
					<div class="col-md-6" align="right">
						<a href = "{{ url('/admin/customer_group/create') }}"><button type="button" class="btn btn-relief-warning mr-1 mb-1">Add</button></a>
					</div>
				</div>
                
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Success</h4>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <p class="mb-0">
                {!! session('message') !!}
                </p>
               
            </div>               
            @endif
           
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors dataTable" id="categories">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Group Title</th>
                                    <th>Type</th>
                                    <th>Discount Value</th>
                                    <th>Discount Type</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer_group as $customer_group)
                                    <tr>
                                        <td>{{ $customer_group->id }}</td>
                                        <td>{{ $customer_group->GroupTitle }}</td>  

                                        
                                        @if($customer_group->type == '1')
                                        <td>Price Up</td>
                                        @else
                                        <td>Discount</td>
                                        @endif
                                        <td>{{ $customer_group->DiscountValue }}</td>
                                        @if($customer_group->DiscountType == '1')
                                        <td>SGD</td>
                                        @else
                                        <td>%</td>
                                        @endif                                        
                                        <td>{{ $customer_group->Details }}</td>
                                        <td>{{ $customer_group->DisplayOrder }}</td>
                                        <td>{{ $customer_group->status }}</td>
                                        <td><a href = "{{ url('/admin/Customer_Group/'.$Customer_Group->id.'/edit') }}">
                                        <button type="button" class="btn btn-relief-success mr-1 mb-1 waves-effect waves-light">
                                        <i class="fa fa-pencil"></i></button></a>
                                        <a href="javascript:void(0);" onclick="chkconfirm('{{ $Customer_Group->id }}')">
                                        <button type="button" class="btn btn-relief-danger mr-1 mb-1 waves-effect waves-light">
                                        <i class="fa fa-trash-o"></i></button></a></td>
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
<script type="text/javascript">
function chkconfirm(id) {
	var chk = confirm('Are you sure! you want to delete this Customer Group?');
	if(chk) {
		window.location = "{{ url('/admin/Customer_Group') }}/"+id+"/destroy";
	}
}
</script>	
<script>

$('.dataex-html5-selectors').DataTable( {
	dom: 'Bfrtip',
	buttons: [
		/*{
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
		}*/
	]
});

</script>