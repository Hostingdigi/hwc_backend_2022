@include('header')
<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="index.html">Home</a></li>
		  <li><div class="pageheading">Checkout</div></li>
        </ol>
      </div>
    </section>
	@php
		$roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
	@endphp
<!-- Checkout -->
<section class="checkout">
	
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<div class="pay-meth">
					<h5>Billing Address</h5>
					<div class='row'>
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>First Name:</b></label> 
							<div>{{ $billinginfo['bill_fname'] }}</div>
						</div>
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>Last Name:</b></label> 
							<div>{{ $billinginfo['bill_lname'] }}</div>
						</div>
					</div> 
					<div class='row' style="margin-top:15px;">
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>Email:</b></label> 
							<div>{{ $billinginfo['bill_email'] }}</div>
						</div>
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>Phone:</b></label> 
							<div>{{ $billinginfo['bill_mobile'] }}</div>
						</div>
					</div>
					<div class='row' style="margin-top:15px;">
						<div class='col-xs-12 col-md-12'>
							<label class='control-label'><b>Address:</b></label> 
							<div>{{ $billinginfo['bill_ads1'] }}
							@if($billinginfo['bill_ads2'] != '')
								<br>{{ $billinginfo['bill_ads2'] }}
							@endif
							</div>
						</div>						
					</div> 	
					<div class='row' style="margin-top:15px;">
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>City/Down:</b></label> 
							<div>{{ $billinginfo['bill_city'] }}</div>
						</div>
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>State/Province:</b></label> 
							<div>{{ $billinginfo['bill_state'] }}</div>
						</div>
					</div>
					<div class='row' style="margin-top:15px;">
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>Country:</b></label> 
							<div>{{ $billinginfo['bill_country'] }}</div>
						</div>
						<div class='col-xs-12 col-md-6'>
							<label class='control-label'><b>Postal/Zip Code:</b></label> 
							<div>{{ $billinginfo['bill_zip'] }}</div>
						</div>
					</div>
					
				</div>				
				<div class="row" style="margin-top:15px; text-align:center;">
					<div class="col-xs-12 col-md-12">
						<div id="paypal-button-container"></div>
						<div id="paypal-button"></div>
					</div>
				</div>
				
			</div>
			
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<div class="order-review">
							<h5>Order Summary</h5>
							<div class="review-box">
								<ul class="list-unstyled">
									<li>Product <span>Total</span></li>
									@if($cartdata)
										@foreach($cartdata[$sesid] as $cartkey => $cart)
										<li class="d-flex justify-content-between">
											<div class="pro">
												<div class="row">
													<div class="col-md-3">
														@if($cart['image'] != '')															
															<img src="{{ url('/uploads/product/'.$cart['image']) }}" alt="{{ $cart['productName'] }}" width="100%">
														@else
															<img src="{{ url('/images/noimage.png') }}" alt="{{ $cart['productName'] }}" width="100%">
														@endif
													</div>
													<div class="col-md-9 no-pm">
														<p>{{ $cart['productName'] }}</p>
														<p class="hint">{{ $cart['qty'] }} X S${{ number_format($roundObj->roundDecimal($cart['price']),2) }}</p>
														@if($cart['productoption'])
															<p class="hint">Option: {{ $cart['productoption'] }}</p>
														@endif
														@if($cart['size'] && 1==2)
															<p class="hint">Size: {{ $cart['size'] }}</p>
														@endif
														@if($cart['productWeight'] && 1==2)
															<p class="hint">Weight: {{ $cart['productWeight'] }} Kg</p>
														@endif	
														@if($cart['color'] && 1==2)
															<p class="hint">Color: {{ $cart['color'] }}</p>
														@endif	
														
													</div>
												</div>
											</div>
											<div class="prc">
												<p>S${{ number_format($roundObj->roundDecimal($cart['total']),2) }}</p>
											</div>
										</li>
										@endforeach
									@endif
									
									<li class="subtot">Sub Total 
									    <span>S${{ number_format(str_replace(',','',$subtotal), 2) }}</span>
										@if(!empty($taxLabelOnly))
                						<small style="font-size:92%;word-break: break-word;"><br>[w/o - {{$taxLabelOnly}}]</small>
                						@endif
									</li>
									<li class="subtot" style="border-top: 1px solid #e5e5e5;">{{ $taxtitle }} <span>S${{ number_format(str_replace(',','',$gst), 2) }}</span></li>
									<li class="subtot" style="border-top: 1px solid #e5e5e5;padding-top:13px;">Shipping ({{ $deliverytype }}) <span>${{ number_format(str_replace(',','',$deliverycost), 2) }}</span></li>
									@if($fuelcharges >0)
									<li>Fuel Charges <span>S${{number_format(str_replace(',','',$fuelcharges), 2) }}</span></li>
									@endif
									@if($handlingfee >0)
									<li>Handling Fee <span>S${{number_format(str_replace(',','',$handlingfee), 2) }}</span></li>
									@endif
									<li>Packaging Fee <span>S${{ number_format(str_replace(',','',$packingfee), 2) }}</span></li>
									@if($discount != 0 && $discounttext != '')<li id="dis">Discount({{ $discounttext }})<span>S${{ number_format(str_replace(',','',$discount), 2) }}</span></li>@endif
									<li>Grand Total <span>S${{ number_format(str_replace(',','',$grandtotal), 2) }}</span></li>
								</ul>
							</div>
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
	</form>
</section>
<!-- End Checkout -->

@include('footer')
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
paypal.Button.render({
  env: '{{ $payenv }}',
 
  client: {
	@if($payenv == 'production')  
	production: '{{ $apikey }}'
	@else
	sandbox: '{{ $apikey }}'
	@endif
  },
  payment: function (data, actions) {
	return actions.payment.create({
	  transactions: [{
		amount: {
		  total: '{{ str_replace("", " ", $grandtotal) }}',
		  currency: '{{ $currency }}'
		}
	  }],
	  redirect_urls: {
		return_url: '{{ url("/") }}/success?orderid={{ $orderincid }}',
		cancel_url: '{{ url("/") }}/cancelpayment?orderid={{ $orderincid }}'
	  }
	});
  },
  onAuthorize: function (data, actions) {
	return actions.payment.execute()
	  .then(function () {
		actions.redirect();
	  });
  },
  onCancel: function (data, actions) {
    actions.redirect();
  }
}, '#paypal-button');

</script>

