@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
}
</style>

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
                            <h2 class="content-header-title float-left mb-0">Manage Order</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/orders/') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
						
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Delivery Tracking</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="multipledelivery_frm" id="multipledelivery_frm" method = "post" action="{{ url('/admin/orders/updatemultipledelivery') }}" enctype="multipart/form-data">    
											<input type="hidden" name="orderid" value="{{ $order->order_id}}">			
											{{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <table class="table table-striped table-bordered">
															<tr>																
																<th>Item</th>
																<th>Unit Price</th>
																<th>Total Qty</th>
																<th width="100">Status</th>
															</tr>
															@if($orderdetails)
																@foreach($orderdetails as $orderdetail)	
																<tr>																	
																	<td>{{ $orderdetail->prod_name }}</td>
																	<td>${{ $orderdetail->prod_unit_price }}</td>
																	<td>
																	@php
																		$delrefundqty = $deliveryinfoid = 0;
																		
																		$orddeldetails = \App\Models\OrderDeliveryDetails::where('order_id', '=', $orderdetail->order_id)->where('prod_id', '=', $orderdetail->prod_id)->select('quantity', 'delivery_info_id' )->first();
																		if($orddeldetails) {
																			$delrefundqty = $orddeldetails->quantity;
																			$deliveryinfoid = $orddeldetails->delivery_info_id;
																		}
																	@endphp
																	{{ $orderdetail->prod_quantity }}</td>
																	<td>Shipped</td>
																</tr>
																
																@endforeach
															@endif
																
															@php
																$shippingby = '';
																$deliveryinfo = \App\Models\OrderDeliveryInfo::where('delivery_id', '=', $deliveryinfoid)->select('shipping_by')->first();
																if($deliveryinfo) {
																	$shippingby = $deliveryinfo->shipping_by;
																}
															@endphp
															
															@if($shippingby == 'Ninja Van')
																<tr>
																	<td colspan="4" align="right"><a href="{{ url('/admin/orders/'.$deliveryinfoid.'/trackorder') }}" class="btn btn-primary mr-1 mb-1" target="_blank">Track Order</a></td>
																</tr>
															@else
																<tr>
																	<td colspan="4" align="right">Shipping By: <b>{{ $shippingby }}</b></td>
																</tr>
															@endif
														</table>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                        </form>
										
                                    </div>
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
