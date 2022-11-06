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
        <div class="content-wrapper" style="width:85%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Role & Rights</h2>                            
                        </div>
						@if(in_array('32_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/roleandrights/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
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
                                <tr style="background:#CCC;">
                                    <th>#</th>    
                                    
									<th>Role Name</th>
									
									<th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               	@foreach($rolerights as $roleright)
                                    <tr>
											<td>{{ $roleright->id }}</td>                                        	
											
											<td>{{ $roleright->role_name }}</td>
											
                                            <td>
												@if(in_array('32_Edit', $moduleaccess) || $adminrole == 0)
                                                <a href="{{ url('/admin/roleandrights/'.$roleright->id.'/edit') }}">
												<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                                                </a>
                                                @endif
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
					<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($rolerights->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($rolerights->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $rolerights->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($rolerights->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $rolerights->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($rolerights->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $rolerights->lastPage()) as $i)
										@if($i >= $rolerights->currentPage() - 2 && $i <= $rolerights->currentPage() + 2)
											@if ($i == $rolerights->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $rolerights->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($rolerights->currentPage() < $rolerights->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($rolerights->currentPage() < $rolerights->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $rolerights->appends($_GET)->url($rolerights->lastPage()) }}">{{ $rolerights->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($rolerights->hasMorePages())
										<li><a href="{{ $rolerights->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
									@else
										<li class="disabled"><span>»</span></li>
									@endif
								</ul>
							@endif
						</div>		
				 
						
            
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
	var chk = confirm('Are you sure! you want to delete this role and rights?');
	if(chk) {
		window.location = "{{ url('/admin/roleandrights') }}/"+id+"/destroy";
	}
}
</script>	

