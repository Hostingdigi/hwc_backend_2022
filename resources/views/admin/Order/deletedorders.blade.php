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
                            <h2 class="content-header-title float-left mb-0">Manage Deleted Orders</h2>                            
                        </div>
						<!--div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/country/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div-->
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <!--th>Select</th-->
                                    <th>Order Id</th>                                                                        
                                    <th>Date</th>
									<th>Customer Name</th>
									<th>Amount</th>
									<th>Invoice Type</th>
									<th>Delivery In</th>									
									<th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($orders) > 0)
                               	@foreach($orders as $order)
                                    <tr>
											<!--td><input type="checkbox" value="{{ $order->order_id }}" name="productids[]"></td-->
                                        	<td>{{ $order->order_id }}</td>
											<td>{{ date('d M Y h:i A', strtotime($order->created_at)) }}</td>
											<td>{{ $order->bill_fname }} {{ $order->bill_lname }}</td>
											<td>${{ $order->payable_amount }}</td>
											<td>
												@if($order->order_type == "1")
													Order
												@else
													Quotation
												@endif
											
											</td>
											<td>
											@php
												$deliveryin = $collectstatus = '';
												$delivery = \App\Models\ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
												if($delivery) {
													if(strpos($delivery->EnName, 'Self Collection') !== false) {
														$deliveryin = 'Self Collection';
													}
												}
											@endphp
											@if($deliveryin != '') {{ $deliveryin }} @elseif($order->delivery_times == 1) Single Delivery @else Multiple Delivery @endif
											</td>
											
											<td align="center">
											@if(in_array('23_View', $moduleaccess) || $adminrole == 0)
											<a href="{{ url('/admin/archiveorder/'.$order->order_id) }}"><i class="fa fa-search" aria-hidden="true"></i></a>
											@endif
											</td>
                                    </tr>
                                @endforeach
							@else
								<tr><td colspan="8" align="center">No Records Found</td></tr>
							@endif	
                            </tbody>
                        </table>
					</div>
					<div class="row" style="padding:10px 0;">
						<div class="col-md-10">
							@if ($orders->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($orders->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $orders->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($orders->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $orders->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($orders->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $orders->lastPage()) as $i)
										@if($i >= $orders->currentPage() - 2 && $i <= $orders->currentPage() + 2)
											@if ($i == $orders->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $orders->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($orders->currentPage() < $orders->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($orders->currentPage() < $orders->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $orders->appends($_GET)->url($orders->lastPage()) }}">{{ $orders->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($orders->hasMorePages())
										<li><a href="{{ $orders->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
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

