@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">My Account</div></li>
        </ol>
      </div>
    </section>
	
 <!-- MyAccount -->
<section class="pt-7 pb-12">
    <div class="container">
        <div class="row">
          <div class="col-12 text-center">

            <!-- Heading -->
            <h3 class="mb-10">Order Details</h3>

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
                      <div class="col-6 col-lg-3">

                        <!-- Heading -->
                        <h6 class="heading-xxxs text-muted">Order No:</h6>

                        <!-- Text -->
                        <p class="mb-lg-0 font-size-sm font-weight-bold">
						@php 
							$orderid = $orders->order_id;
						@endphp
						@if(strlen($orders->order_id) == 3)
							@php $orderid = '0'.$orders->order_id; @endphp
						@elseif(strlen($orders->order_id) == 2)
							@php $orderid = '00'.$orders->order_id; @endphp
						@elseif(strlen($orders->order_id) == 1)
							@php $orderid = '000'.$orders->order_id; @endphp	
						@endif

						@php
							$orderid = date('Ymd', strtotime($orders->date_entered)).$orderid;
						@endphp
						
						{{ $orderid }}
                        </p>

                      </div>
                      <div class="col-6 col-lg-3">

                        <!-- Heading -->
                        <h6 class="heading-xxxs text-muted">Shipped date:</h6>

                        <!-- Text -->
                        <p class="mb-lg-0 font-size-sm font-weight-bold">
                          <time datetime="2019-10-01">
                            
                          </time>
                        </p>

                      </div>
                      <div class="col-6 col-lg-3">

                        <!-- Heading -->
                        <h6 class="heading-xxxs text-muted">Status:</h6>

                        <!-- Text -->
                        <p class="mb-0 font-size-sm font-weight-bold">
							@if($orders->order_status == 0) Payment Pending @elseif($orders->order_status == 1) Paid, Shipping Pending @elseif($orders->order_status == 2) Shipped @elseif($orders->order_status == 3) Shipped @elseif($orders->order_status == 5) On the Way To You @elseif($orders->order_status == 6) Partially Delivered @elseif($orders->order_status == 7) Partially Refund @elseif($orders->order_status == 8) Fully Refund @elseif($orders->order_status == 9) Ready For Collection @endif
                        </p>

                      </div>
                      <div class="col-6 col-lg-3">

                        <!-- Heading -->
                        <h6 class="heading-xxxs text-muted">Order Amount:</h6>

                        <!-- Text -->
                        <p class="mb-0 font-size-sm font-weight-bold">
							${{ number_format($orders->payable_amount, 2) }}
                        </p>

                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <div class="card-footer">

                <!-- Heading -->
                <h6 class="mb-7">Order Items ({{ count($orderdetails) }})</h6>

                <!-- Divider -->
                <hr class="my-5">
				
				<div class="card card-lg border">
				  <div class="card-body">

					<!-- Heading -->
					<h6 class="mb-7">Billing & Shipping Details</h6>

					<!-- Content -->
					<div class="row">
					  <div class="col-12 col-md-4">

						<!-- Heading -->
						<p class="mb-4 font-weight-bold">
						  Billing Address:
						</p>

						<p class="mb-7 mb-md-0 text-gray-500">
						{{ $orders->bill_fname.' '.$orders->bill_lname }}<br>
						{{ $orders->bill_ads1 }}<br>
						@if($orders->bill_ads2){{ $orders->bill_ads2 }}<br>@endif
						{{ $orders->bill_city }}<br>
						{{ $orders->bill_state }}<br>
						{{ $orders->bill_country }}<br><br>
						{{ $orders->bill_email }}<br>
						{{ $orders->bill_mobile }}<br>
						</p>

					  </div>
					  <div class="col-12 col-md-4">

						<!-- Heading -->
						<p class="mb-4 font-weight-bold">
						  Shipping Address:
						</p>

						<p class="mb-7 mb-md-0 text-gray-500">
							{{ $orders->ship_fname.' '.$orders->ship_lname }}<br>
							{{ $orders->ship_ads1 }}<br>
							@if($orders->ship_ads2){{ $orders->ship_ads2 }}<br>@endif
							{{ $orders->ship_city }}<br>
							{{ $orders->ship_state }}<br>
							{{ $orders->ship_country }}<br><br>
							{{ $orders->ship_email }}<br>
							{{ $orders->ship_mobile }}<br>
						</p>

					  </div>
					  <div class="col-12 col-md-4">

						<!-- Heading -->
						<p class="mb-4 font-weight-bold">
						  Shipping Method:
						</p>

						<p class="mb-7 text-gray-500">
							@php
								$shipping = \App\Models\ShippingMethods::where('Id', '=', $orders->ship_method)->select('EnName')->first();
							@endphp
							@if($shipping)
								{{ $shipping->EnName }}
							@endif
						</p>

						<!-- Heading -->
						<p class="mb-4 font-weight-bold">
						  Payment Method:
						</p>

						<p class="mb-0 text-gray-500">
						{{ $orders->pay_method }}
						</p>

					  </div>
					</div>
					
					
					
				  </div>
				</div>
				
				<!-- Divider -->
                <hr class="my-5">

                <!-- List group -->
                <ul class="list-group list-group-lg list-group-flush-y list-group-flush-x">
					@foreach($orderdetails as $orderdetail)
						@php
							$productimage = $uniquekey = $productcolor = $productsize = $productweight = '';
							$product = \App\Models\Product::where('Id', '=', $orderdetail->prod_id)->select('Image', 'UniqueKey', 'Color', 'Size', 'Weight')->first();
							if($product) {
								$productimage = $product->Image;
								$uniquekey = $product->UniqueKey;
								$productsize = $product->Size;
								$productcolor = $product->Color;
								$productweight = $product->Weight;
							}
						@endphp
						<li class="list-group-item">
							<div class="row align-items-center">
								<div class="col-4 col-md-3 col-xl-2">
									@if($productimage != '')										
										<a href="{{ url('/prod/'.$uniquekey) }}"><img src="{{ url('/uploads/product/'.$productimage) }}" alt="{{ $orderdetail->prod_name }}" class="img-fluid"></a>
									@else
										<a href="{{ url('/prod/'.$uniquekey) }}"><img src="{{ url('/images/noimage.png') }}" alt="{{ $orderdetail->prod_name }}" class="img-fluid"></a>
									@endif	
									
								</div>
								
								<div class="col-6 col-md-7">
									<!-- Title -->
									<p class="mb-4 font-size-sm font-weight-bold">
										<a class="text-body" href="{{ url('/prod/'.$uniquekey) }}">{{ $orderdetail->prod_name }}</a><br>
										@if($orderdetail->prod_option != '')
											<span class="text-muted">Option: {{ $orderdetail->prod_option }}</span><br>
										@endif
										@if($productweight != '')
											<span class="text-muted">Weight: {{ $productweight }} Kg</span><br>
										@endif
										@if($productsize != '')
											<span class="text-muted">Size: {{ $productsize }}</span><br>
										@endif
										@if($productcolor != '')
											<span class="text-muted">Color: {{ $productcolor }}</span><br>
										@endif
										<span class="text-muted">${{ $orderdetail->prod_unit_price * $orderdetail->prod_quantity }}</span>
									</p>
									<!-- Text -->
									<div class="font-size-sm text-muted">
										<span>{{ $orderdetail->prod_quantity }} X ${{ $orderdetail->prod_unit_price }}</span>
									</div>
								</div>
								<div class="col-4 col-md-2">
									<!-- Title -->
									<p class="mb-4 font-size-sm font-weight-bold">
										Total Qty: {{ $orderdetail->prod_quantity }}
										@php
											$delivered = $refunded = 0;
											$deliverydetails = \App\Models\OrderDeliveryDetails::where('order_id', '=', $orders->order_id)->where('prod_id', '=', $orderdetail->prod_id)->get();
										@endphp
										@if($deliverydetails)
											@foreach($deliverydetails as $deliverydetail)											
												@if($deliverydetail->status == 1)
													@php 
														$delivered = $delivered + $deliverydetail->quantity;
													@endphp	
												@elseif($deliverydetail->status == 2)
													@php
														$refunded = $refunded + $deliverydetail->quantity;
													@endphp
												@endif	
											@endforeach
										@endif	
										@if($delivered > 0)
										<br>Delivered: {{ $delivered }}
										@endif
										@if($refunded > 0)
										<br>Refunded: {{ $refunded }}
										@endif
									</p>
									
								</div>
							</div>
						</li>
					@endforeach
                  
                </ul>

              </div>
            </div>

            <!-- Total -->
            <div class="card card-lg mb-5 border">
              <div class="card-body">

                <!-- Heading -->
                <h6 class="mb-7">Order Total</h6>

                <!-- List group -->
                <ul class="list-group list-group-sm list-group-flush-y list-group-flush-x">
                  <li class="list-group-item d-flex">
                    <span>Subtotal</span>
                    <span class="ml-auto">${{ number_format(($orders->payable_amount + $orders->discount_amount) - ($orders->shipping_cost + $orders->packaging_fee + $orders->tax_collected), 2) }}</span>
                  </li>
                  <li class="list-group-item d-flex">
                    <span>Tax</span>
                    <span class="ml-auto">${{ number_format($orders->tax_collected, 2) }}</span>
                  </li>
                  <li class="list-group-item d-flex">
                    <span>Shipping</span>
                    <span class="ml-auto">${{ number_format($orders->shipping_cost, 2) }}</span>
                  </li>
				  <li class="list-group-item d-flex">
                    <span>Packaging Fee</span>
                    <span class="ml-auto">${{ number_format($orders->packaging_fee, 2) }}</span>
                  </li>
				  @if($orders->discount_id > 0)
						@php
							$coupondata = [];
							$coupondata = \App\Models\Couponcode::where('id', '=', $orders->discount_id)->first();
						@endphp
						
						@if($coupondata)
							@if($coupondata->discount_type == 1)
								<li class="list-group-item d-flex">
									<span>Discount({{ $coupondata->discount.'%' }})</span>
									<span class="ml-auto">${{ number_format($orders->discount_amount, 2) }}</span>
								</li>
							@else
								<li class="list-group-item d-flex">
									<span>Discount({{ '$'.$coupondata->discount }})</span>
									<span class="ml-auto">${{ number_format($orders->discount_amount, 2) }}</span>
								</li>
							@endif
						@endif	
					@endif	
                  <li class="list-group-item d-flex font-size-lg font-weight-bold">
                    <span>Grand Total</span>
                    <span class="ml-auto">${{ number_format($orders->payable_amount, 2) }}</span>
                  </li>
                </ul>

              </div>
            </div>
			<!--div class="row">
				<div class="col-md-12">
					Disclaimer: Price/ Discount rate is subject to change without prior notice
				</div>
			</div-->
			<div class="row">
						<div class="col-md-8"></div>
					  <div class="col-12 col-md-4">
						@if($orders->order_status == 0)
						<a class="btn btn-lg btn-block btn-outline-dark" href="{{ url('/makepayment/'.$orders->order_id) }}">
							 PROCEED PAYMENT 
						</a>						
						@else
							<!--a class="btn btn-lg btn-block btn-outline-dark" href="{{ url('/downloadinvoice/'.$orders->order_id) }}"-->
						<!--a href="#" class="btn btn-lg btn-block btn-outline-dark">
							 DOWNLOAD INVOICE
						</a-->
						@endif
					  </div>
					</div>
            <!-- Details -->
            

          </div>
    </div>
</section>
<!-- End MyAccount -->




@include('footer')
    </body>
</html>
