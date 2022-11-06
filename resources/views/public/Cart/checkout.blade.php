@include('header')
<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><a href="{{ url('/cart') }}">Cart</a></li>
		  <li><div class="pageheading">Checkout</div></li>
        </ol>
      </div>
    </section>
	
<!-- Checkout -->
	
	<section class="checkout">
		<form action="{{ url('/placeorder') }}" method="post" name="checkoutfrm" id="checkoutfrm" onsubmit="return checkoutvalidate();">
		@csrf
		<input type="hidden" name="user_id" id="user_id" value="@if($customer){{ $customer->cust_id }}@endif">
		<div class="container">
			<div class="row">
				<div class="col-md-7">                        
						<h5>Billing Information</h5>
						<div class="row">
							<div class="col-md-6">
								<label>First Name*</label>
								<input type="text" name="bill_fname" required id="bill_fname" value="@if($customer){{ $customer->cust_firstname }}@endif" placeholder="Your first name">
							</div>
							<div class="col-md-6">
								<label>Last Name*</label>
								<input type="text" name="bill_lname" required id="bill_lname" value="@if($customer){{ $customer->cust_lastname }}@endif" placeholder="Your last name">
							</div>
							<div class="col-md-6">
								<label>Email Address*</label>
								<input type="text" name="bill_email" required id="bill_email" value="@if($customer){{ $customer->cust_email }}@endif" placeholder="Your email address">
							</div>
							<div class="col-md-6">
								<label>Phone*</label>
								<input type="text" name="bill_mobile" required value="@if($customer){{ $customer->cust_phone }}@endif" placeholder="Your phone number">
							</div>
							<div class="col-md-12">
								<label>Company Name</label>
								<input type="text" name="bill_compname" value="@if($customer){{ $customer->cust_company }}@endif" placeholder="Your company name (optional)">
							</div>
							<div class="col-md-12">
								<label>Address*</label>
								<input type="text" name="bill_ads1" required value="@if($customer){{ $customer->cust_address1 }}@endif" placeholder="Address line 1">
								<input type="text" name="bill_ads2" value="@if($customer){{ $customer->cust_address2 }}@endif" placeholder="Address line 2">
							</div>
							<div class="col-md-6 contry">
								<label>Country*</label>								
								<select class="country" name="bill_country" id="bill_country" required>
									<option value="">Select Country</option>
									@if($countries)
										@foreach($countries as $country)
											<option value="{{ $country->countrycode }}"  @if($customer) @if($country->countryid == $customer->cust_country || $country->countrycode == $customer->cust_country) selected @endif @endif>{{ $country->countryname }}</option>
										@endforeach
									@endif                                        
								</select>
							</div>
							<div class="col-md-6">
								<label>Town/City*</label>
								<input type="text" name="bill_city" required value="@if($customer){{ $customer->cust_city }}@endif" placeholder="Your town or city name">
							</div>
							<div class="col-md-6">
								<label>State/Province*</label>
								<input type="text" name="bill_state" required value="@if($customer){{ $customer->cust_state }}@endif" placeholder="Your state or province">
							</div>
							<div class="col-md-6">
								<label>Postal/Zip Code*</label>
								<input type="text" name="bill_zip" required value="@if($customer){{ $customer->cust_zip }}@endif" placeholder="Your postal or zip code">
							</div>
							
							<div class="col-md-12">
								<ul class="list-unstyled">                                        
									<li><input type="checkbox" value="1" id="shipaddress" name="shipaddress" checked><label for="shipaddress">Ship To Same Address?</label></li>
								</ul>
							</div>
							
							<div class="col-md-12" id="showshipaddress" style="display:none;">
								<h5>Shipping Information</h5>
								<div class="row">
									<div class="col-md-6">
										<label>First Name*</label>
										<input type="text" name="ship_fname" id="ship_fname" value="" placeholder="Your first name">
									</div>
									<div class="col-md-6">
										<label>Last Name*</label>
										<input type="text" name="ship_lname" id="ship_lname" value="" placeholder="Your last name">
									</div>
									<div class="col-md-6">
										<label>Email Address*</label>
										<input type="text" name="ship_email" id="ship_email" value="" placeholder="Your email address">
									</div>
									<div class="col-md-6">
										<label>Phone*</label>
										<input type="text" name="ship_mobile" id="ship_mobile" value="" placeholder="Your phone number">
									</div>									
									<div class="col-md-12">
										<label>Address*</label>
										<input type="text" name="ship_ads1" id="ship_ads1" value="" placeholder="Address line 1">
										<input type="text" name="bill_ads2" value="" placeholder="Address line 2">
									</div>
									<div class="col-md-6 contry">
										<label>Country*</label>
										<select class="country" name="ship_country" id="ship_country" >
											<option value="">Select Country</option>
											@if($countries)
												@foreach($countries as $country)
													<option value="{{ $country->countrycode }}">{{ $country->countryname }}</option>
												@endforeach
											@endif                                        
										</select>
									</div>
									<div class="col-md-6">
										<label>Town/City*</label>
										<input type="text" name="ship_city" id="ship_city" value="" placeholder="Your town or city name">
									</div>
									<div class="col-md-6">
										<label>State/Province*</label>
										<input type="text" name="ship_state" id="ship_state" value="" placeholder="Your state or province">
									</div>
									<div class="col-md-6">
										<label>Postal/Zip Code*</label>
										<input type="text" name="ship_zip" id="ship_zip" value="" placeholder="Your postal or zip code">
									</div>
								</div>
							</div>
							
							
							<div class="col-md-12 contry">
								<label>If Item(s) Unavailable*</label>								
								<select class="country" name="if_items_unavailabel" id="if_items_unavailabel" required>
									<option value="">Please Select</option>
									<option value="1" @if(Session::has('if_unavailable')) @if(Session::get('if_unavailable') == '1') selected @endif @endif>Call me</option>
									<option value="2" @if(Session::has('if_unavailable')) @if(Session::get('if_unavailable') == '2') selected @endif @endif>Do not replace</option>
									<option value="3" @if(Session::has('if_unavailable')) @if(Session::get('if_unavailable') == '3') selected @endif @endif>Replace with similar item(s)</option>
								</select>
							
							</div>
						</div>
					
				</div>
				<div class="col-md-5">
					<div class="row">
						<div class="col-md-12">
							<div class="order-review">
								<h5>Order Review</h5>
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
														<p class="hint">{{ $cart['qty'] }} X ${{ number_format($cart['price'],2) }}</p>
														@if($cart['productoption'])
															<p class="hint">Option: {{ $cart['productoption'] }}</p>
														@endif
														@if($cart['size'])
															<p class="hint">Size: {{ $cart['size'] }}</p>
														@endif	
														@if($cart['productWeight'])
															<p class="hint">Weight: {{ $cart['productWeight'] }} Kg</p>
														@endif	
														@if($cart['color'])
															<p class="hint">Color: {{ $cart['color'] }}</p>
														@endif	
														
													</div>
												</div>
												</div>
												<div class="prc">
													<p>${{ number_format($cart['total'],2) }}</p>
												</div>
											</li>
											@endforeach
										@endif
										
										<li>Sub Total <span>${{ $subtotal }}</span></li>
										<li id="tax">{{ $taxtitle }} <span>${{ $gst }}</span></li>
										@if($discount != 0 && $discounttext != '')<li id="dis">Discount({{ $discounttext }})<span>${{ number_format($discount, 2) }}</span></li>@endif
										<li id="grandtotal">Grand Total <span>${{ $grandtotal }}</span></li>
									</ul>
								</div>
							</div>
						</div>
						
						
						<div class="col-md-12" style="font-size:16px;" id="freedeliverymsg">
							
							<strong>{{ $showfreedeliverymsg }}</strong>
							
						</div>
						
						
						<div class="col-md-12">
							<div class="pay-meth">
								<h5>Delivery Method</h5>
								<div class="pay-box">
									<ul class="list-unstyled" id="shipmethods">
										@if($deliverymethods)
											@foreach($deliverymethods as $deliverymethod)
											<li>
												<input type="radio" id="deliverymethod{{ $deliverymethod->Id }}" name="deliverymethod" value="{{ $deliverymethod->Id }}" @if(Session::has('deliverymethod')) @if(Session::get('deliverymethod') == $deliverymethod->Id) checked @endif @endif>
												<label for="deliverymethod{{ $deliverymethod->Id }}"><span><i class="fa fa-circle"></i></span>{{ $deliverymethod->EnName }}</label>
											</li>
											@endforeach
										@endif
									</ul>
									<span id="deliveryerror" style="display:none; color:red;">Select Delivery Method!</span>
								</div>
							</div>								
							<button type="submit" name="button" class="ord-btn" id="placeorder">Proceed</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
	</section>
	
    <!-- End Checkout -->
@include('footer')

<script type="text/javascript">
$(document).ready(function() {		

	$('#shipaddress').change(function() { 
		if ($("#shipaddress").is(":checked")) {
			$('#ship_fname').prop('required', '');
			$('#ship_lname').prop('required', '');
			$('#ship_email').prop('required', '');
			$('#ship_mobile').prop('required', '');
			$('#ship_country').prop('required', '');
			$('#ship_city').prop('required', '');
			$('#ship_state').prop('required', '');
			$('#ship_ads1').prop('required', '');
			$('#ship_zip').prop('required', '');
			$('#showshipaddress').hide();
		} else {
			$('#ship_fname').prop('required', 'required');
			$('#ship_lname').prop('required', 'required');
			$('#ship_email').prop('required', 'required');
			$('#ship_mobile').prop('required', 'required');
			$('#ship_country').prop('required', 'required');
			$('#ship_city').prop('required', 'required');
			$('#ship_state').prop('required', 'required');
			$('#ship_ads1').prop('required', 'required');
			$('#ship_zip').prop('required', 'required');
			$('#showshipaddress').show();
		}
	});
	
	$('#bill_country').change(function() {
		if($('#shipaddress').is(':checked')) {
			var country = $('#bill_country').val();
			var subtotal = '{{ $subtotal }}';
			var token = "{!! csrf_token() !!}";
			$.ajax({
				type: "POST",
				url: '{{ url("/") }}/getdeliverymethods',				
				data: {'country': country, 'subtotal': subtotal},
				headers: {'X-CSRF-TOKEN': token},		
				success: function(response) {
					//console.log(response);
					if(response != '') {
						var resultarr = response.split('|-|');
						var result = resultarr[0].split('|#|');
						if(result.length > 0) {
							$('#shipmethods').html('');
							var deliverytypes = '';
							var shippingmethods = '';
							var shippingid = shippingname = '';
							for(var i = 0; i < result.length; i++) {
								deliverytypes = result[i].split('||');
								shippingid = deliverytypes[0];
								shippingname = deliverytypes[1];
								shippingmethods += '<li><input type="radio" id="deliverymethod'+shippingid+'" name="deliverymethod" value="'+shippingid+'">	<label for="deliverymethod'+shippingid+'"><span><i class="fa fa-circle"></i></span>'+shippingname+'</label></li>';
							}
							$('#shipmethods').html(shippingmethods);
						} else {
							$('#shipmethods').html('No Delivery Methods');
						}
						$('#freedeliverymsg').html('<strong>'+resultarr[1]+'</strong>');
					} else {
						$('#shipmethods').html('No Delivery Methods');
					}
				},error: function(ts) {				
					console.log("Error:"+ts.responseText);  
				}
			});
		}
	});
	
	$('#ship_country').change(function() {
		
		var country = $('#ship_country').val();
		var subtotal = '{{ $subtotal }}';
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/getdeliverymethods',				
			data: {'country': country, 'subtotal': subtotal},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {
				//console.log(response);
				if(response != '') {
					var resultarr = response.split('|-|');
					var result = resultarr[0].split('|#|');
					if(result.length > 0) {						
						var deliverytypes = '';
						var shippingmethods = '';
						var shippingid = shippingname = '';
						for(var i = 0; i < result.length; i++) {
							
							deliverytypes = result[i].split('||');
							//console.log(deliverytypes);
							shippingid = deliverytypes[0];
							shippingname = deliverytypes[1];
							
							shippingmethods += '<li><input type="radio" id="deliverymethod'+shippingid+'" name="deliverymethod" value="'+shippingid+'">	<label for="deliverymethod'+shippingid+'"><span><i class="fa fa-circle"></i></span>'+shippingname+'</label></li>';
						}
						$('#shipmethods').html(shippingmethods);
					} else {
						$('#shipmethods').html('No Delivery Methods');
					}
					$('#freedeliverymsg').html('<strong>'+resultarr[1]+'</strong>');
				} else {
					$('#shipmethods').html('No Delivery Methods');
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});
		
	});
});	

function checkoutvalidate() {
	var allowsubmit = 0;
	$('input[name="deliverymethod"]').each(function(){
		if(this.checked) {
			allowsubmit++;
		}
	});
	if(allowsubmit > 0) {
		$('#deliveryerror').hide();
		return true;
	} else {		
		$('#deliveryerror').show();
		return false;
	}
}

</script>
