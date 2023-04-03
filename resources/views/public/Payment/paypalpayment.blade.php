

@php
    $disAmt = !empty($order->discount_amount) ? $order->discount_amount : 0;
@endphp
<div>
    <table border="1" width="90%" style="margin:0 auto;">
        <tr><td colspan="2"><b>Billing Address</b></td></tr>
        <tr><td>First Name</td><td>{{ $order->bill_fname }}</td></tr>
        <tr><td>Last Name</td><td>{{ $order->bill_lname }}</td></tr>
        <tr><td>Email</td><td>{{ $order->bill_email }}</td></tr>
        <tr><td>Phone</td><td>{{ $order->bill_mobile }}</td></tr>
        <tr><td>Address</td><td>{{ $order->bill_ads1 }}</td></tr>
        @if($order->bill_ads2 != '')
        <tr><td></td><td>{{ $order->bill_ads2 }}</td></tr>
        @endif
        <tr><td>City/Down</td><td>{{ $order->bill_city }}</td></tr>
        <tr><td>State</td><td>{{ $order->bill_state }}</td></tr>
        <tr><td>Country</td><td>{{ $order->bill_country }}</td></tr>
        <tr><td>Zip Code</td><td>{{ $order->bill_zip }}</td></tr>
        <tr><td colspan="2"><b>Order Summary</b></td></tr>
        <tr><td>Sub Total</td><td>${{ ($order->payable_amount + $disAmt) - ($order->tax_collected + $order->shipping_cost + $order->packaging_fee)  }}</td></tr>
        <tr><td>Tax</td><td>${{ $order->tax_collected }}</td></tr>
        <tr><td>Shipping</td><td>${{ $order->shipping_cost }}</td></tr>
        <tr><td>Packaging Fee</td><td>${{ $order->packaging_fee }}</td></tr>
        @if(!empty($discounttext))
        <tr><td>{{$discounttext}}</td><td>${{ !empty($disAmt) ? $disAmt : '0.00' }}</td></tr>
        @endif
        <tr><td>Grand Total</td><td>${{ $order->payable_amount }}</td></tr>
    </table>
</div>
<div class="row" style="margin-top:15px; text-align:center;">
					<div class="col-xs-12 col-md-12">
						<div id="paypal-button-container"></div>
						<div id="paypal-button"></div>
					</div>
				</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
		return_url: '{{ url("/") }}/successorder?orderid={{ $orderid }}',
		cancel_url: '{{ url("/") }}/cancelorder?orderid={{ $orderid }}'
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

