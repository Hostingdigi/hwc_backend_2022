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
        <div class="content-wrapper" style="width:60%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Admin Access</h2>                            
                        </div>
						
						@if(in_array('33_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/manageadmin/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
						
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr  style="background:#CCC;">
                                    <th>Id</th>
                                    <th>Admin Name</th>  									 	                                                                      
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@foreach($manageadmins as $manageadmin)
                                    <tr>
									<td>{{ $manageadmin->id }}</td>
									<td>{{ $manageadmin->username }}</td>                                                        		                                                                           
                                	<td>
									@if(in_array('33_Edit', $moduleaccess) || $adminrole == 0)
                                    <a href="{{ url('/admin/manageadmin/'.$manageadmin->id.'/edit') }}">
                                    <i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>
                                    &nbsp;&nbsp;|&nbsp;&nbsp;
									@endif
									
									@if(in_array('33_Delete', $moduleaccess) || $adminrole == 0)
                                    <a href="javascript:void(0);" onclick="chkdelete({{ $manageadmin->id }})">
                                    <i class="fa fa-trash fa-lg" aria-hidden="true"></i></a> 
									@endif
									</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
							
							<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($manageadmins->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($manageadmins->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $manageadmins->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($manageadmins->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $manageadmins->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($manageadmins->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $manageadmins->lastPage()) as $i)
										@if($i >= $manageadmins->currentPage() - 2 && $i <= $manageadmins->currentPage() + 2)
											@if ($i == $manageadmins->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $manageadmins->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($manageadmins->currentPage() < $manageadmins->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($manageadmins->currentPage() < $manageadmins->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $manageadmins->appends($_GET)->url($manageadmins->lastPage()) }}">{{ $manageadmins->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($manageadmins->hasMorePages())
										<li><a href="{{ $manageadmins->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
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
	var chk = confirm('Are you sure! you want to delete this Admin?');
	if(chk) {
		window.location = "{{ url('/admin/manageadmin') }}/"+id+"/destroy";
	}
}
</script>	
