@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->


<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:90%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div style="width:100%;" class="mb-5">
                    <button onclick="printDiv('printMe')" style="margin-right:130px !important;" class="btn btn-primary float-right m-1">Print</button>
                </div>
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title mb-0" style="text-align:center;"></h2>                            
                        </div>
						<!--div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/country/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div-->
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section style="width:80%; margin:0 auto;" id="printMe">
            
                <div class="table-responsive" style="width:100%; margin:0 auto; border:1px solid #ccc; padding:0 20px;">
					<table>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><th style="text-align:center;" colspan="2"><img src="{{ url('/img/logo.png') }}"></th></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td align="center" colspan="2">
						@php
							$orderid = $orders->order_id;
							if(strlen($orderid) == 3) {
								$orderid = date('Ymd', strtotime($orders->date_entered)).'0'.$orderid;
							} elseif(strlen($orderid) == 2) {
								$orderid = date('Ymd', strtotime($orders->date_entered)).'00'.$orderid;
							} elseif(strlen($orderid) == 1) {
								$orderid = date('Ymd', strtotime($orders->date_entered)).'000'.$orderid;
							} else {
								$orderid = date('Ymd', strtotime($orders->date_entered)).$orderid;
							}
						@endphp
						Invoice #: {{ $orderid }} </td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td width="50%">
								<table>									
									<tr><td>{!! nl2br($settings->company_address) !!}</td></tr>									
									<tr><td>Fax: {{ $settings->company_fax }}</td></tr>
									<tr><td>GST No: {{ $settings->GST_res_no }}</td></tr>
								</table>
							</td>
							<td width="50%" class="float-right">
								@php
									$shipping = \App\Models\ShippingMethods::where('Id', '=', $orders->ship_method)->select('EnName')->first();
								@endphp								
								<table>
									<tr><td>Date: {{ date('d/m/Y', strtotime($orders->date_entered)) }}</td></tr>
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
								@if(!empty($orders->bill_compname))
								<b><br>Billing Company Name</b>
                                    <table>
									<tbody><tr><td>{{ $orders->bill_compname }}</td></tr>
								</tbody></table>
								@endif
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td colspan="2">								
								<table class="table-bordered" width="100%" cellpadding="8">
									<tr><th width="40%">Item</th><th width="12%" style="text-align:center;">Quantity</th><th width="18%" style="text-align:right">Price</th><th width="20%" style="text-align:right">Status</th><th width="20%" style="text-align:right">Total</th></tr>
									@if($orderdetails)
										@foreach($orderdetails as $orderdetail)	
											@php
												$product = \App\Models\Product::where('Id', '=', $orderdetail->prod_id)->first();
											@endphp
											<tr>
												<td>{{ $orderdetail->prod_name }}<br>
												@if($orderdetail->prod_option != '')
													Option: {{ $orderdetail->prod_option }}<br>
												@endif
												@if($product->Weight != '' && 1==2)
													Weight: {{ $product->Weight }} Kg<br>
												@endif
												@if($product->Size != '')
													Size: {{ $product->Size }}<br>
												@endif
												@if($product->Color != '')
													Color: {{ $product->Color }}<br>
												@endif
												</td>
												<td style="text-align:center;">{{ $orderdetail->prod_quantity }}</td>
												<td style="text-align:right">${{ $orderdetail->prod_unit_price }}</td>
												<td style="text-align:right">
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
												Delivered: {{ $delivered }}<br>
												@endif
												@if($refunded > 0)
												Refunded: {{ $refunded }}<br>
												@endif
												</td>
												<td style="text-align:right">${{ number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) }}</td>
											</tr>	
										@endforeach
									@endif
									<tr><td colspan="4">&nbsp;</td></tr>
									<tr>
										<td colspan="2"></td><td colspan="2">Sub Total</td>
										<td style="text-align:right">${{ number_format($orders->payable_amount - ($orders->shipping_cost + $orders->packaging_fee + $orders->tax_collected), 2) }}</td>										
									</tr>
									<tr>
										<td colspan="2"></td><td colspan="2">Tax</td>
										<td style="text-align:right">${{ number_format($orders->tax_collected, 2) }}</td>
									</tr>
									<tr>
										<td colspan="2"></td><td colspan="2">Shipping</td>
										<td style="text-align:right">${{ number_format($orders->shipping_cost, 2) }}</td>
									</tr>
									<tr>
										<td colspan="2"></td><td colspan="2">Packaging Fee</td>
										<td style="text-align:right">${{ number_format($orders->packaging_fee, 2) }}</td>
									</tr>
									<tr>
										<td colspan="2"></td><td colspan="2"><b>Grand Total</b></td>
										<td style="text-align:right"><b>${{ number_format($orders->payable_amount, 2) }}</b></td>
									</tr>
								</table>
							</td>
							
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td colspan="2">
							Note : Prices are subjected to change without prior notice. We hope that our quotation is favourable to you and looking forward to receive your valued orders in due course.<br><br>Thank and regards.
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
					</table>    
                </div>


            </section>
            </div>
        </div>
</div>





    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
    <script>
        function printDiv(divName){
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;

			document.body.innerHTML = printContents;

			window.print();

			document.body.innerHTML = originalContents;

		}
    </script>
@include('admin.includes.footer')
