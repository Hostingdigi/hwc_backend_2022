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
        <div class="content-wrapper" style="width:90%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Coupon Code</h2>                            
                        </div>
						@if(in_array('17_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/couponcode/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
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
                                    <th>Select</th>                                              
                                    <th>Coupon Code</th>
                                    <th>Validity</th>
                                    <th>No of times</th>
                                    <th>Discount</th>
                                    <th>Customer Type</th>									
									<th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($couponcodes) > 0)
                               	@foreach($couponcodes as $couponcode)
                                    <tr>
										<td><input type="checkbox" name="ordnum[]" value="1" onclick="check_request_quote_selection(this,'');"></td>
										<td>{{ $couponcode->coupon_code }}</td>
										<td>{{ $couponcode->validity }}</td>
										<td>{{ $couponcode->nooftimes }}</td>
										<td>@if($couponcode->discount_type == 1) {{ $couponcode->discount.'%' }} @else {{ '$'.$couponcode->discount }} @endif</td>
										<td>@if($couponcode->customer_type == 1) Individual @else Overall @endif</td>
										<td>
										@if(in_array('17_Status', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $couponcode->id }}, {{ $couponcode->status }})">
										@if($couponcode->status == 1)<i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else
										<i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;
										@endif
										
										@if(in_array('17_Edit', $moduleaccess) || $adminrole == 0)
											<a href="{{ url('/admin/couponcode/'.$couponcode->id.'/edit') }}">
											<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
											</a>
											&nbsp;&nbsp;
										@endif
										@if(in_array('17_Delete', $moduleaccess) || $adminrole == 0)
											<a href="javascript:void(0);" onclick="chkdelete({{ $couponcode->id }})">
												<i class="fa fa-trash fa-lg" aria-hidden="true"></i>
											</a>
										@endif	
										</td>
                                    </tr>
                                @endforeach
							@else
								<tr><td colspan="7" align="center">No Records Found</td></tr>
							@endif	
                            </tbody>
                        </table>
                </div>
				
				<div class="row" style="padding:10px 0;">
					<div class="col-md-10">
						@if ($couponcodes->hasPages())
							<ul class="pagination pagination">
								{{-- Previous Page Link --}}
								@if ($couponcodes->onFirstPage())
									<li class="disabled"><span>«</span></li>
								@else
									<li><a href="{{ $couponcodes->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
								@endif

								@if($couponcodes->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $couponcodes->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($couponcodes->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $couponcodes->lastPage()) as $i)
									@if($i >= $couponcodes->currentPage() - 2 && $i <= $couponcodes->currentPage() + 2)
										@if ($i == $couponcodes->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $couponcodes->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($couponcodes->currentPage() < $couponcodes->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($couponcodes->currentPage() < $couponcodes->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $couponcodes->appends($_GET)->url($couponcodes->lastPage()) }}">{{ $couponcodes->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($couponcodes->hasMorePages())
									<li><a href="{{ $couponcodes->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
								@else
									<li class="disabled"><span>»</span></li>
								@endif
							</ul>
						@endif
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
	var chk = confirm('Are you sure! you want to delete this couponcode?');
	if(chk) {
		window.location = "{{ url('/admin/couponcode') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this couponcode?');
	if(chk) {
		window.location = "{{ url('/admin/couponcode') }}/"+id+"/"+status+"/updatestatus";
	}
}
</script>	

