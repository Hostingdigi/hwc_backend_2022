@include('header')
<!-- Shopping Cart -->
<section class="shopping-cart">
	<div class="container">
		@includeIf('public.messages')
			
		<div class="alert dancer" id="notavailable" style="display:none;"></div>
			
		@if(!empty($cartdata))
		
		<div class="row">
			<div class="col-md-8">
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
								@foreach($cartdata as $cartkey => $cart)
									@php
										$productid = $cart['productId'];
										$product = \App\Models\Product::where('Id', '=', $productid)->select('UniqueKey', 'Quantity', 'cust_qty_per_day')->first();
										$maxqty = $product->Quantity;
										if(($product->cust_qty_per_day < $product->Quantity) && $product->cust_qty_per_day > 0) {
											$maxqty = $product->cust_qty_per_day;
										}
									@endphp	
									<tr>
										<td class="t-pro d-flex">
											<div class="t-img">
												<a href="{{ url('/prod/'.$product->UniqueKey) }}">
													<img src="{{ (!empty($cart['image']) ? env('IMG_URL').('/uploads/product/'.$cart['image']) : url('/images/noimage.png')) }}" alt="{{ $cart['productName'] }}" width="100" title="{{ $cart['productName'] }}">
												</a>
											</div>
											<div class="t-content">
												<p class="t-heading"><a href="{{ url('/prod/'.$product->UniqueKey) }}">{{ $cart['productName'] }}</a></p>
												@if($cart['productoption'])
													<p>Option: {{ $cart['productoption'] }}</p>
												@endif
											</div>
										</td>
										<td class="t-price" style="width:15%;">S${{ number_format($cart['price'], 2) }}</td>
										<td class="t-qty" style="width:14%;">
											<div class="qty-box">
												<div class="quantity buttons_added">
													<input type="button" value="-" class="minus" onclick="updateqty('minus', '{{ $productid }}', '{{ $maxqty }}', '{{ $cart['productName'] }}', '{{isset($cart['option_id'])?$cart['option_id']:'' }}');">
													<input type="number" step="1" min="1" max="10" value="{{ $cart['qty'] }}" class="qty text" id="qty{{ $cart['productId'] }}_{{isset($cart['option_id'])?$cart['option_id']:'' }}" size="4" readonly name="qty{{ $cart['productId'] }}">
													<input type="button" value="+" class="plus" onclick="updateqty('plus', '{{ $productid }}', '{{ $maxqty }}', '{{ $cart['productName'] }}', '{{isset($cart['option_id'])?$cart['option_id']:'' }}');">
													<input type="hidden" name="cartitems[]" value="{{ $cart['productId'] }}">
												</div>
											</div>
										</td>
										<td class="t-total" style="width:15%;" id="t-total{{ $cart['productId'] }}_{{isset($cart['option_id'])?$cart['option_id']:'' }}">S${{ number_format($cart['total'], 2) }}</td>
										<td class="t-rem" style="width:5%;"><a href="{{ url('/removecartitem/'.$cartkey.'/'.$cart['productId']) }}"><i class="fa fa-trash-o"></i></a></td>
									</tr>
								@endforeach						
							</tbody>
						</table>
					</form>
				</div>
			</div>
			<div class="col-md-4">
				<div class="crt-sumry">
					<h5>Cart Summary</h5>
					<ul class="list-unstyled">
						<li>Subtotal
							<span id="subtotal">S${{ $subtotal }}</span>
							@if(!empty($taxLabelOnly))
                    		<small style="word-break: break-word;"><br>[w/o - {{$taxLabelOnly}}]</small>
                    		@endif
						</li>
						<li>{{ $taxtitle }} <span id="gst">S${{ $gst }}</span></li>						
						<li id="dis" style="display:none;"></li>
						<li class="mt-3" style="font-size: 18px;color:#000;font-weight: 900 !important;">Grand Total <span id="grandtotal" style="color:#000 !important;">S${{ number_format(($subtotal+$gst),2) }}</span></li>
					</ul>
					<div class="cart-btns text-right">
						<button type="button" class="chq-out">Checkout</button>
					</div>
				</div>
				
				<div class="crt-sumry" style="background:none; padding:23px 10px 30px;">
					<div class="cart-btns text-right">
						<button type="button" class="up-cart" style="margin-right:10px;" id="clearcart">Clear Cart</button>
					</div>
				</div>
				
			</div>
			<div class="col-md-4">
				<div class="coupon">
					<h6>Discount Coupon</h6>
					<p>Enter your coupon code if you have one</p>
					<div class="alert dancer" id="notlogin" style="display:none;"></div>
					<div class="alert success" id="applied" style="display:none;"></div>
					<form action="#">
						<input type="text" name="couponcode" id="couponcode" value="{{ $couponcode }}" placeholder="Your Coupon">
						<a type="button" class="redbtn" id="applycoupon">Apply Code</a>
						<a href="{{ url('/cancelcoupon') }}" class="btn btn-warning" id="cancelcoupon" style="@if($couponcode == '') display:none; @endif float:right;">Cancel Coupon</a>
					</form>
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
	
	$('#applycoupon').click(function() {
		var couponcode = $('#couponcode').val();
		var customer = '';		
		
		var cartitems = $("input[name='cartitems[]']").map(function () {
			return this.value; // $(this).val()
		}).get();
		if(cartitems) {
			cartitems = cartitems.join('|');
		}
		
		@if(Session::has('customer_id'))
			customer = "{{ Session::get('customer_id') }}";
		@endif	
		if(customer != '') {
			if(couponcode != '') {
				var token = "{!! csrf_token() !!}";		
				$.ajax({
					type: "POST",
					url: '{{ url("/") }}/applycouponcode',				
					data: {'customer': customer, 'couponcode': couponcode, 'cartitems': cartitems},
					headers: {'X-CSRF-TOKEN': token},		
					success: function(response) {				
						console.log(response);
						if(response != '') {
							if(response == 'Coupon Code Usage Limit Exceeded!' || response == 'Invalid Coupon Code!' || response == 'Coupon Code Expired!') {
								$('#notlogin').html(response);
								$('#notlogin').show();
								$('#cancelcoupon').hide();
								$('#applycoupon').show();
							} else {
								var result = response.split('|');
								if(result.length > 0) {
									$('#notlogin').html('');
									$('#notlogin').hide();
									$('#subtotal').html('$'+result[1]);
									$('#gst').html('$'+result[2]);
									$('#dis').html('Discount ('+result[4]+') <span id="discountamt">$'+result[0]+'</span>');
									$('#dis').show();
									$('#grandtotal').html('$'+result[3]);
									$('#cancelcoupon').show();
									$('#applycoupon').hide();
								}
							}
						} else {
							$('#notlogin').html('Invalid Coupon Code!');
							$('#notlogin').show();
							$('#cancelcoupon').hide();
							$('#applycoupon').show();
						}
					},error: function(ts) {				
						console.log("Error:"+ts.responseText);  
					}
				});
			}
		} else {
			$('#notlogin').html('Please login to use coupon code!');
			$('#notlogin').show();
		}
	});
	
	@if(Session::has('couponcode'))
		@if(Session::get('couponcode') != '')
			var couponcode = "{{ Session::get('couponcode') }}";
			var customer = '';		
			
			var cartitems = $("input[name='cartitems[]']").map(function () {
				return this.value; // $(this).val()
			}).get();
			if(cartitems) {
				cartitems = cartitems.join('|');
			}
			
			@if(Session::has('customer_id'))
				customer = "{{ Session::get('customer_id') }}";
			@endif	
			if(customer != '') {
				if(couponcode != '') {
					var token = "{!! csrf_token() !!}";		
					$.ajax({
						type: "POST",
						url: '{{ url("/") }}/applycouponcode',				
						data: {'customer': customer, 'couponcode': couponcode, 'cartitems': cartitems},
						headers: {'X-CSRF-TOKEN': token},		
						success: function(response) {				
							console.log(response);
							if(response != '') {
								if(response == 'Coupon Code Usage Limit Exceeded!' || response == 'Invalid Coupon Code!' || response == 'Coupon Code Expired!') {
									$('#notlogin').html(response);
									$('#notlogin').show();
									$('#cancelcoupon').hide();
								} else {
									var result = response.split('|');
									if(result.length > 0) {
										$('#notlogin').html('');
										$('#notlogin').hide();
										$('#subtotal').html('$'+result[1]);
										$('#gst').html('$'+result[2]);
										$('#dis').html('Discount ('+result[4]+') <span id="discountamt">$'+result[0]+'</span>');
										$('#dis').show();
										$('#grandtotal').html('$'+result[3]);
										$('#cancelcoupon').show();
									}
								}
							} else {
								$('#notlogin').html('Invalid Coupon Code!');
								$('#notlogin').show();
								$('#cancelcoupon').hide();
							}
						},error: function(ts) {				
							console.log("Error:"+ts.responseText);  
						}
					});
				}
			}
		@endif	
	@endif
});
	
function updateqty(action, productid, maxqty, prodname, optionid) {
	var qty = $('#qty'+productid+"_"+optionid).val();
	if(action == 'minus') {
		if(qty > 1) {
			qty = parseInt(qty) - 1;
			$('#qty'+productid+"_"+optionid).val(qty);
		}
	} else {
		qty = parseInt(qty) + 1;
		$('#qty'+productid+"_"+optionid).val(qty);
	}
	if(qty >= 1) {
		if(qty > maxqty) {
			$('#notavailable').html('Required quantity not available for "'+prodname+'"');
			$('#notavailable').show();
		} else {
			$('#notavailable').html('');
			$('#notavailable').hide();
			var token = "{!! csrf_token() !!}";		
			$.ajax({
				type: "POST",
				url: '{{ url("/") }}/updatecartqty',				
				data: {'prodid': productid, 'qty': qty, 'optionid': optionid},
				headers: {'X-CSRF-TOKEN': token},		
				success: function(response) {				
					console.log(response);
					if(response != '') {
						var result = response.split('|');
						if(result.length > 0) {
							$('#t-total'+productid+"_"+optionid).html('$'+result[0]);
							$('#subtotal').html('$'+result[1]);
							$('#gst').html('$'+result[2]);
							$('#grandtotal').html('$'+result[3]);
							$('#discountamt').html('$'+result[4]);
						}
					} else {
						
					}
				},error: function(ts) {				
					console.log("Error:"+ts.responseText);  
				}
			});
		}
	}
}
</script>