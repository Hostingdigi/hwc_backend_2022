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
                            <h2 class="content-header-title float-left mb-0">Manage Local Shipping Method</h2>                            
                        </div>
						@if(in_array('30_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/local_shipping_methods/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
	 <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-bordered table-striped dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>ID</th>
                                    <th>Method</th>                                                                        
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($Local_shipping_methods as $Local_shipping_method)
                                    <tr>
                                        <td>{{ $Local_shipping_method->Id }}</td>
										<td>{{ $Local_shipping_method->EnName }}</td>
										<td> 
										@if(in_array('30_Status', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $Local_shipping_method->Id }}, {{ $Local_shipping_method->Status }})">
											@if($Local_shipping_method->Status != 0) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif 
										</a> &nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('30_Edit', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/local_shipping_methods/'.$Local_shipping_method->Id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('30_Delete', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkdelete({{ $Local_shipping_method->Id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
										@endif
										</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
							
							<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($Local_shipping_methods->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($Local_shipping_methods->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $Local_shipping_methods->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($Local_shipping_methods->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $Local_shipping_methods->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($Local_shipping_methods->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $Local_shipping_methods->lastPage()) as $i)
										@if($i >= $Local_shipping_methods->currentPage() - 2 && $i <= $Local_shipping_methods->currentPage() + 2)
											@if ($i == $Local_shipping_methods->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $Local_shipping_methods->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($Local_shipping_methods->currentPage() < $Local_shipping_methods->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($Local_shipping_methods->currentPage() < $Local_shipping_methods->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $Local_shipping_methods->appends($_GET)->url($Local_shipping_methods->lastPage()) }}">{{ $Local_shipping_methods->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($Local_shipping_methods->hasMorePages())
										<li><a href="{{ $Local_shipping_methods->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
									@else
										<li class="disabled"><span>»</span></li>
									@endif
								</ul>
							@endif
						</div>		
				 
            
					</div>
                </div>


            </section>
            </div>
        </div>
</div>





    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')
	
<script type="text/javascript">
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this c ?');
	if(chk) {
		window.location = "{{ url('/admin/local_shipping_methods') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this Local Shipping Method ?');
	if(chk) {
		window.location = "{{ url('/admin/local_shipping_methods') }}/"+id+"/"+status+"/updatestatus";
	}
}
</script>	
