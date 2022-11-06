@include('header')

<style>
.table-bordered td, .table-bordered th { padding:6px; }
</style>

<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="index.html">Home</a></li>
		  <li><div class="pageheading">Invoice</div></li>
        </ol>
      </div>
    </section>
	
<!-- Checkout -->
<section class="checkout">
	<form action="{{ url('/paymentform') }}" method="post" name="placeorderfrm" id="placeorderfrm" onsubmit="return placeordervalidate();">	
	@csrf
	<div class="container">
		<div class="row">
			@if($bannerads)
				<div class="col-md-3">
				@foreach($bannerads as $bannerad)
					<div class="add-box">
						@if($bannerad->Video != '')
							<div class="embed-responsive embed-responsive-16by9">
								{!! $bannerad->Video !!}
							</div> 
						@else
							<a href="{{ $bannerad->ban_link }}"><img src="{{ url('/').'/uploads/bannerads/'.$bannerad->EnBanimage }}" alt="{{ $bannerad->ban_name }}" class="img-fluid"></a>
						@endif
					</div>
				@endforeach
				</div>
				<div class="col-md-9">
			@else	
				<div class="col-md-12">
			@endif	
				<div class="row">
					<div class="col-md-12">						
						<h5 class="text-center">Invoice Details</h5>
						<div class="table-responsive" style="width:100%; margin:0 auto; border:1px solid #ccc; padding:0 20px;">
							<table>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr><th style="text-align:center;" colspan="2"><img src="{{ url('/img/logo.png') }}"></th></tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr><td align="center" colspan="2">
								@php
									$orderid = $orders->order_id;
									if(strlen($orderid) == 3) {
										$orderid = date('Ymd', strtotime($orders->created_at)).'0'.$orderid;
									} elseif(strlen($orderid) == 2) {
										$orderid = date('Ymd', strtotime($orders->created_at)).'00'.$orderid;
									} elseif(strlen($orderid) == 1) {
										$orderid = date('Ymd', strtotime($orders->created_at)).'000'.$orderid;
									} else {
										$orderid = date('Ymd', strtotime($orders->created_at)).$orderid;
									}
								@endphp
								Invoice #: {{ $orderid }} </td></tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td width="50%">
										<table>											
											<tr><td>@php echo nl2br($settings->company_address); @endphp </td></tr>											
											<tr><td>Fax: {{ $settings->company_fax }}</td></tr>
											<tr><td>GST No: {{ $settings->GST_res_no }}</td></tr>
										</table>
									</td>
									<td width="50%" class="float-right">
										@php
											$shipping = \App\Models\ShippingMethods::where('Id', '=', $orders->ship_method)->select('EnName')->first();
										@endphp								
										<table>
											<tr><td>Date: {{ date('d/m/Y', strtotime($orders->created_at)) }}</td></tr>
											<tr><td>Status: @if($orders->order_status == 0) Payment Pending @elseif($orders->order_status == 1) Paid, Shipping Pending @elseif($orders->order_status == 3) Shipped @elseif($orders->order_status == 4) Delivered @elseif($orders->order_status == 5) On The Way To You @elseif($orders->order_status == 6) Partially Delivered @elseif($orders->order_status == 7) Partially Refunded @elseif($orders->order_status == 8) Fully Refunded @elseif($orders->order_status == 9) Ready For Collection @endif</td></tr>
											<tr><td>Delivery Method: 
											@if($shipping)
												{{ $shipping->EnName }}
											@endif</td></tr>
											<tr><td>If item(s) unavailable: @if($orders->if_items_unavailabel == 1) Call Me @elseif($orders->if_items_unavailabel == 2) Do Not Replace @else Replace @endif</td></tr>									
										</table>
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td width="50%" >
										<b>Shipping Info</b>
										<table>
											<tr><td>{{ $orders->ship_fname.' '.$orders->ship_lname }}</td></tr>
											<tr><td>{{ $orders->ship_ads1 }}</td></tr>
											@if($orders->ship_ads2)
											<tr><td>{{ $orders->ship_ads2 }}</td></tr>
											@endif
											<tr><td>{{ $orders->ship_city }}</td></tr>
											<tr><td>{{ $orders->ship_state }} - {{ $orders->ship_zip }}</td></tr>
											<tr><td>{{ $orders->ship_country }}</td></tr>
										</table>
									</td>
									<td width="50%" class="float-right">
										<b>Billing Info</b>
										<table>
											<tr><td>{{ $orders->bill_fname.' '.$orders->bill_lname }}</td></tr>
											<tr><td>{{ $orders->bill_ads1 }}</td></tr>
											@if($orders->bill_ads2)
											<tr><td>{{ $orders->bill_ads2 }}</td></tr>
											@endif
											<tr><td>{{ $orders->bill_city }}</td></tr>
											<tr><td>{{ $orders->bill_state }} - {{ $orders->bill_zip }}</td></tr>
											<tr><td>{{ $orders->bill_country }}</td></tr>
										</table>
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td colspan="2">								
										<table class="table-bordered" width="100%" cellpadding="8">
											<tr><th width="40%">Item</th><th width="15%" style="text-align:center;">Quantity</th><th width="25%" style="text-align:right">Price</th><th width="20%" style="text-align:right">Total</th></tr>
											@if($orderdetails)
												@foreach($orderdetails as $orderdetail)
													@php
														$product = \App\Models\Product::where('Id', '=', $orderdetail->prod_id)->first();
													@endphp
													<tr>
														<td>
														{{ $orderdetail->prod_name }}<br>
														@if($orderdetail->prod_option != '')
															<span class="text-muted">Option: {{ $orderdetail->prod_option }}</span><br>
														@endif
														@if($product->Weight != '')
															<span class="text-muted">Weight: {{ $product->Weight }} Kg</span><br>
														@endif
														@if($product->Size != '')
															<span class="text-muted">Size: {{ $product->Size }}</span><br>
														@endif
														@if($product->Color != '')
															<span class="text-muted">Color: {{ $product->Color }}</span><br>
														@endif
														</td>
														<td style="text-align:center;">{{ $orderdetail->prod_quantity }}</td>
														<td style="text-align:right">${{ $orderdetail->prod_unit_price }}</td>
														<td style="text-align:right">${{ number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) }}</td>
													</tr>											
												@endforeach
											@endif
											<tr><td colspan="4">&nbsp;</td></tr>
											<tr>
												<td colspan="2"></td><td>Sub Total</td>
												<td style="text-align:right">${{ number_format($orders->payable_amount - ($orders->shipping_cost + $orders->packaging_fee + $orders->tax_collected), 2) }}</td>										
											</tr>
											<tr>
												<td colspan="2"></td><td>Tax</td>
												<td style="text-align:right">${{ number_format($orders->tax_collected, 2) }}</td>
											</tr>
											<tr>
												<td colspan="2"></td><td>Shipping</td>
												<td style="text-align:right">${{ number_format($orders->shipping_cost, 2) }}</td>
											</tr>
											<tr>
												<td colspan="2"></td><td>Packaging Fee</td>
												<td style="text-align:right">${{ number_format($orders->packaging_fee, 2) }}</td>
											</tr>
											@if($orders->discount_amount != '0.00')
											<tr>
												<td colspan="2"></td><td>Discount</td>
												<td style="text-align:right">${{ number_format($orders->discount_amount, 2) }}</td>
											</tr>
											@endif
											<tr>
												<td colspan="2"></td><td><b>Grand Total</b></td>
												<td style="text-align:right"><b>${{ number_format($orders->payable_amount, 2) }}</b></td>
											</tr>
										</table>
									</td>
									
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td colspan="2">
									Note : Prices are subjected to change without prior notice. We hope that our quotation is favourable to you and looking forward to receive your valued orders in due course.<br><br>Thank and Regards.
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr><td colspan="2">&nbsp;</td></tr>
							</table>    
						</div>
						
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Checkout -->

@include('footer')

