@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Orders</div></li>
        </ol>
      </div>
    </section>
	
 <!-- MyAccount -->
<section class="pt-7 pb-12">
    <div class="container">
        <div class="row">
          <div class="col-12 text-center">

            <!-- Heading -->
            <h3 class="mb-10">Track Order</h3>

          </div>
        </div>
        <div class="row">
			<div class="col-12 col-md-3">
            <!-- Nav -->
            @includeIf('public/Customer.leftnav')
			</div>
			
			<div class="col-12 col-md-9">

            <!-- Order -->
            <div class="card card-lg mb-5 border">
              <div class="card-body pb-0">

                <!-- Info -->
                <div class="card card-sm">
                  <div class="card-body bg-light">
                    <div class="row">
                      <div class="col-12 col-lg-12">
						@php
							$orderincid = $orderid;
						@endphp
						
						@if(strlen($orderid) == 3)
							@php $orderid = '0'.$orderid; @endphp
						@elseif(strlen($orderid) == 2)
							@php $orderid = '00'.$orderid; @endphp
						@elseif(strlen($orderid) == 1)
							@php $orderid = '000'.$orderid; @endphp	
						@endif

						@php
							$orderid = date('Ymd', strtotime($orderdate)).$orderid;
						@endphp
                        <!-- Heading -->
                        <h6 class="heading-xxxs text-muted text-center">Order No: {{ $orderid }}</h6>

                      </div>
                      
                    </div>
					
                  </div>
                </div>
				<div class="row">
					@if(count($orderdeliverydetails) > 0)
						
						
						<div class="col-12">
							<table class="table table-striped table-bordered">
								<tr>
									<th>Date</th>
									<th>Item(s)</th>									
									<th>Next Delivery Date</th>
									<th>Tracking No</th>															
								</tr>
								@foreach($orderdeliverydetails as $orderdeliverydetail)
									<tr>
										<td>
										
										@php	
										
										
											$nextdeliverydate = $shippingby = $trackingnumber = $deldate = '';
											$deliveryinfo = \App\Models\OrderDeliveryInfo::where('delivery_id', '=', $orderdeliverydetail->delivery_info_id)->where('order_id', '=', $orderdeliverydetail->order_id)->select('next_delivery_date', 'shipping_by', 'ship_tracking_number', 'delivered_date')->first();
											if($deliveryinfo) {
												$deldate = $deliveryinfo->delivered_date;
											}
										@endphp
										@if($deldate != '' && $deldate != '0000-00-00')
											{{ date('d M Y', strtotime($deldate)) }}
										@endif
										</td>
										<td>
										@php
											$productname = '';
											$productids = [];
											$deldetails = \App\Models\OrderDeliveryDetails::where('delivery_info_id', '=', $orderdeliverydetail->delivery_info_id)->where('order_id', '=', $orderdeliverydetail->order_id)->where('detail_id', '=', $orderdeliverydetail->detail_id)->get();
											if($deldetails) {
												
												foreach($deldetails as $deldetail) {
													
													$orddetails = \App\Models\OrderDetails::where('detail_id', '=', $deldetail->detail_id)->select('prod_name', 'prod_option')->get();
													foreach($orddetails as $orddetail) {
														if(!in_array($deldetail->prod_id, $productids)) {
															
															$productcolor = $productsize = $productweight = '';
															$product = \App\Models\Product::where('Id', '=', $orderdeliverydetail->prod_id)->select('Image', 'UniqueKey', 'Color', 'Size', 'Weight')->first();
															if($product){
																$productsize = $product->Size;
																$productcolor = $product->Color;
																$productweight = $product->Weight;
															}
															
															echo '<b>'.$orddetail->prod_name.'</b><br>';
															if($orddetail->prod_option != '') {
																
																echo 'Option: '.$orddetail->prod_option.'<br>';
															}
															if($productweight != '') {
																echo 'Weight: '.$productweight.' Kg<br>';
															}
															if($productsize != '') {
																echo 'Size: '.$productsize.'<br>';
															}
															if($productcolor != '') {
																echo 'Color: '.$productcolor.'<br>';
															}
															if($orderdeliverydetail->status == 1) { 
																echo '<b>Shipped : </b>'; 
															} else {
																echo '<b>Refunded : </b>'; 
															}
															echo '<b>'.$orderdeliverydetail->quantity.'</b>';
															$productids[] = $deldetail->prod_id;
														}
													}
												}
											}
										@endphp
										</td>
										
										<td>
										@php										
											
											if($deliveryinfo) {
												$nextdeliverydate = $deliveryinfo->next_delivery_date;
												$shippingby = $deliveryinfo->shipping_by;
												$trackingnumber = $deliveryinfo->ship_tracking_number;
											}
										@endphp
										@if($nextdeliverydate != '' && $nextdeliverydate != '0000-00-00') 
										{{ date('d M Y', strtotime($nextdeliverydate)) }}
										@endif	
										</td>
										<td>
										@if($orderdeliverydetail->status == 1)
											@if($shippingby != '')			
												<b>{{ $trackingnumber }}</b><br>
												<!--a href="{{ url('/trackorder/'.$orderdeliverydetail->order_id.'/'.$trackingnumber) }}" class="site-btn bg-dark mx-auto textyellow" target="_blank" style="white-space:nowrap;">Track Order</a-->
											@endif
										@endif	
										</td>
									</tr>
								@endforeach
							</table>
						</div>
							
					@endif
				</div>
              </div>
              
    </div>
</section>
<!-- End MyAccount -->

@include('footer')
