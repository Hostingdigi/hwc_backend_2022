@include('header')
<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><a href="{{ url('/cart') }}">Cart</a></li>
		  <li><a href="{{ url('/checkout') }}">Shipping</a></li>
		  <li><div class="pageheading">Place Order</div></li>
        </ol>
      </div>
    </section>
	
<!-- Checkout -->
<section class="checkout">
	<form action="{{ url('/paymentform') }}" method="post" name="placeorderfrm" id="placeorderfrm" onsubmit="return placeordervalidate();">	
	@csrf
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<div class="pay-meth">
					<h5>Payment Method</h5>
					@if(Session::has('error'))
						<div class="alert error">{{ Session::get('error') }}</div>
					@endif
					<div class="pay-box">
						<ul class="list-unstyled">
						@php
							$arry_cus_lists = array(30548,30197,30334);
							
						@endphp
							@if($paymentmethods)
								@foreach($paymentmethods as $paymentmethod)
								@if($paymentmethod->Id==5 && in_array($sesid,$arry_cus_lists))
								        <li>
									<input type="radio" id="paymethod{{ $paymentmethod->Id }}" name="paymentmethod" value="{{ $paymentmethod->Id }}" >
									<label for="paymethod{{ $paymentmethod->Id }}"><span><i class="fa fa-circle"></i></span><img style="height:22px; margin:5px 0px 10px;" src="{{ url('/uploads/images/'.$paymentmethod->payment_logo) }}" /></label>
								</li>
									@endif
								@if($paymentmethod->Id!=5)
								    
								<li>
									<input type="radio" id="paymethod{{ $paymentmethod->Id }}" name="paymentmethod" value="{{ $paymentmethod->Id }}" >
									<label for="paymethod{{ $paymentmethod->Id }}"><span><i class="fa fa-circle"></i></span><img style="height:22px; margin:5px 0px 10px;" src="{{ url('/uploads/images/'.$paymentmethod->payment_logo) }}" /></label>
								</li>
								@endif
								
								@endforeach
							@endif
						</ul>
						<span id="paymenterror" style="display:none; color:red;">Select Payment Method!</span>
					</div>
				</div>				
				
				<div class="row">
					<div class="col-md-6 col-sm-12">
						<button type="submit" name="button" class="ord-btn">Place Order</button>
					</div>
					<div class="col-md-6 col-sm-12">
						@if($ordertype == 1)
						<button type="button" name="button" class="ord-btn" id="quotation">Convert to Quotation</button>
						@endif
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
										@foreach($cartdata as $cartkey => $cart)
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
														<p class="hint">{{ $cart['qty'] }} X S${{ number_format($cart['price'],2) }}</p>
														@if($cart['productoption'])
															<p class="hint">Option: {{ $cart['productoption'] }}</p>
														@endif
													
														
													</div>
												</div>
											</div>
											<div class="prc">
												<p>S${{ number_format($cart['total'],2) }}</p>
											</div>
										</li>
										@endforeach
									@endif
									
									<li class="subtot">Sub Total 
									<span>S${{ number_format(str_replace(',','',$subtotal),2) }}</span>
									@if(!empty($taxLabelOnly))
            						<small style="font-size:95%;word-break: break-word;"><br>[w/o - {{$taxLabelOnly}}]</small>
            						@endif
									</li>
									<li class="subtot" style="border-top: 1px solid #e5e5e5; padding-top:12px;">{{ $taxtitle }} <span>S${{ number_format(str_replace(',','',$gst), 2) }}</span></li>
									<li class="subtot" style="border-top: 1px solid #e5e5e5; padding-top:12px;">Shipping ({{ $deliverytype }}) <span>S${{ number_format(str_replace(',','',$deliverycost), 2) }}</span></li>

									@if($fuelcharges >0)
										<li>{{env('FUEL_SUBCHARGE')}}% Fuel Surcharges<span>S${{ number_format(str_replace(',','',$fuelcharges), 2) }}</span></li>
									@endif
									@if($handlingfee >0)
										<li>Handling Fee<span>S${{ number_format(str_replace(',','',$handlingfee), 2) }}</span></li>
									@endif

									<li>Packaging Fee <span>S${{ number_format($packingfee,2) }}</span></li>
									@if($discount != 0 && $discounttext != '')<li id="dis">Discount({{ $discounttext }})<span>S${{ number_format(str_replace(',','',$discount), 2) }}</span></li>@endif
									<li style="font-size: 29px;font-weight: bolder;">Grand Total <span>S${{ number_format(str_replace(',','',$grandtotal),2) }}</span></li>

								</ul>
							</div>
						</div>
					</div>
					@if( Session::get('customer_id')==30548 || 1==1)
						<input type="hidden" name="billinginfo" value="{{Session::has('billinginfo')?json_encode(Session::get('billinginfo')):''}}">
						<input type="hidden" name="deliverymethod" value="{{Session::has('deliverymethod')?Session::get('deliverymethod'):''}}">
						<input type="hidden" name="if_unavailable" value="{{Session::has('if_unavailable')?Session::get('if_unavailable'):''}}">
						<input type="hidden" name="delivery_instructions" value="{{Session::has('delivery_instructions')?Session::get('delivery_instructions'):''}}">
					@endif
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
	$('#quotation').click(function() {
		window.location = "{{ url('/quotation') }}";
	});
});
function placeordervalidate() {
	var allowsubmit = 0;
	$('input[name="paymentmethod"]').each(function(){
		if(this.checked) {
			allowsubmit++;
		}
	});
	if(allowsubmit > 0) {
		$('#paymenterror').hide();
		return true;
	} else {		
		$('#paymenterror').show();
		return false;
	}
}
</script>