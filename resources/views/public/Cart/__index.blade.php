@include('header')
<!-- Shopping Cart -->
<section class="shopping-cart">
	<div class="container">
		@includeIf('public.messages')
			
		@if(!empty($cartdata))
		
		<div class="row">
			<div class="col-md-12">
				<div class="cart-table table-responsive">	
					<form action="{{ url('/updatecart') }}" method="post" name="updatecartfrm" id="updatecartfrm">
					@csrf
					<table class="table">
						<thead>
							<tr>
								<th class="t-pro">Product</th>
								<th class="t-price">Price</th>
								<th class="t-qty">Quantity</th>
								<th class="t-total">Total</th>
								<th class="t-rem"></th>
							</tr>
						</thead>
						<tbody>
							@php 
								$productqty = $productid = '';
							@endphp
							@foreach($cartdata[$sesid] as $cartkey => $cart)
								@php
									$productqty = $cart['qty'];
									$productid = $cart['productId'];
								@endphp	
								<tr>
									<td class="t-pro d-flex">
										<div class="t-img">
											<a href="#">
												@if($cart['image'] != '')															
													<img src="{{ url('/uploads/product/'.$cart['image']) }}" alt="{{ $cart['productName'] }}" width="100">
												@else
													<img src="{{ url('/images/noimage.png') }}" alt="{{ $cart['productName'] }}" width="100">
												@endif
											</a>
										</div>
										<div class="t-content">
											<p class="t-heading"><a href="#">{{ $cart['productName'] }}</a></p>
											<ul class="list-unstyled list-inline rate">
												<li class="list-inline-item"><i class="fa fa-star"></i></li>
												<li class="list-inline-item"><i class="fa fa-star"></i></li>
												<li class="list-inline-item"><i class="fa fa-star"></i></li>
												<li class="list-inline-item"><i class="fa fa-star"></i></li>
												<li class="list-inline-item"><i class="fa fa-star-o"></i></li>
											</ul>
											
										</div>
									</td>
									<td class="t-price">${{ number_format($cart['price'], 2) }}</td>
									<td class="t-qty">
										<div class="qty-box">
											<div class="quantity buttons_added">
												<input type="button" value="-" class="minus" onclick="updateqty('minus', '{{ $productid }}');">
												<input type="number" step="1" min="1" max="10" value="{{ $cart['qty'] }}" class="qty text" id="qty{{ $cart['productId'] }}" size="4" readonly name="qty{{ $cart['productId'] }}">
												<input type="button" value="+" class="plus" onclick="updateqty('plus', '{{ $productid }}');">
												<input type="hidden" name="cartitems[]" value="{{ $cart['productId'] }}">
											</div>
										</div>
									</td>
									<td class="t-total">${{ number_format($cart['total'], 2) }}</td>
									<td class="t-rem"><a href="{{ url('/removecartitem/'.$cartkey.'/'.$cart['productId']) }}"><i class="fa fa-trash-o"></i></a></td>
								</tr>
									
							@endforeach						
							
						</tbody>
					</table>
					<div class="crt-sumry" style="background:none;">
						<div class="cart-btns text-right">
							<button type="button" class="up-cart" style="margin-right:10px;" id="clearcart">Clear Cart</button>
							<button type="button" class="up-cart" id="updatecart">Update Cart</button>						
						</div>
					</div>
					
					</form>
				</div>
			</div>
			<div class="col-md-4">
				<div class="shipping">
					<h6>Calculate Shipping and Tax</h6>
					<p>Enter your destination to get shipping estimate</p>
					<form action="#">
						<div class="country-box">
							<select class="country">
								<option>Select Country</option>
								<option>United State</option>
								<option>United Kingdom</option>
								<option>Germany</option>
								<option>Australia</option>
							</select>
						</div>
						<div class="state-box">
							<select class="state">
								<option>State/Province</option>
								<option>State 1</option>
								<option>State 2</option>
								<option>State 3</option>
								<option>State 4</option>
							</select>
						</div>
						<div class="post-box">
							<input type="text" name="zip" value="" placeholder="Zip/Postal Code">
							<a type="button" class="site-btn bg-dark mx-auto textyellow">Get Estimate</a>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-4">
				<div class="coupon">
					<h6>Discount Coupon</h6>
					<p>Enter your coupon code if you have one</p>
					<form action="#">
						<input type="text" name="zip" value="" placeholder="Your Coupon">
						<a type="button" class="site-btn bg-dark mx-auto textyellow">Apply Code</a>
					</form>
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="crt-sumry">
					<h5>Cart Summary</h5>
					<ul class="list-unstyled">
						<li>Subtotal <span>${{ $subtotal }}</span></li>
						<li>GST (7%) <span>${{ $gst }}</span></li>
						<li>Grand Total <span>${{ $grandtotal }}</span></li>
					</ul>
					<div class="cart-btns text-right">
						<!--button type="button" class="up-cart">Update Cart</button-->
						<button type="button" class="chq-out">Checkout</button>
					</div>
				</div>
			</div>
		</div>
		@else
			
			<div class="row">
				<div class="col-md-12">
					<div align="center">
					<p>You have no items in your shopping cart.</p>
					<p style="margin-top:15px;"><a href="{{ url('/') }}">Click here</a> to continue shopping.</p>
					</div>
				</div>
			</div>
		
		@endif
	</div>
</section>
<!-- End Shopping Cart -->
@include('footer')
<script type="text/javascript">
$(document).ready(function() {
	$('#clearcart').click(function() {
		window.location = "{{ url('/clearcart') }}";
	});
	
	$('#updatecart').click(function() {
		$('#updatecartfrm').submit();
	});
	
	$('.chq-out').click(function() {
		window.location = "{{ url('/checkout') }}";
	});
});
	
function updateqty(action, productid) {
	var qty = $('#qty'+productid).val();
	if(action == 'minus') {
		if(qty > 1) {
			qty = parseInt(qty) - 1;
			$('#qty'+productid).val(qty);
		}
	} else {
		qty = parseInt(qty) + 1;
		$('#qty'+productid).val(qty);
	}
}
</script>