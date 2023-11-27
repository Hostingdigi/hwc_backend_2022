@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->
<style>
    @media print {
      .print-hide {
        display: none;
      }
    }
</style>

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:90%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div style="width:100%;" class="mb-5">
                    <button onclick="printDiv('printMe')" style="margin-right:67px !important;" class="btn btn-primary float-right m-1">Print</button>
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
            <section style="width:90%; margin:0 auto;" id="printMe">
            
                <div class="table-responsive" style="width:100%; margin:0 auto; border:1px solid #ccc; padding:0 20px;color:#000">
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
							$orderid = trim($orderid.$orders->order_alt_text);

						@endphp
						@if($orders->order_type == 2 && $orders->order_status == 0) Quotation @else Invoice @endif #: {{ $orderid }} </td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td width="65%">
								<table>									
									<tr><td>{!! nl2br($settings->company_address) !!}</td></tr>									
									<tr><td>Fax: {{ $settings->company_fax }}</td></tr>
									<tr><td>GST No: {{ $settings->GST_res_no }}</td></tr>
								</table>
							</td>
							<td width="35%" style="vertical-align:top;">
								@php
									$shippinginfo = '';
									$shipping = \App\Models\ShippingMethods::where('Id', '=', $orders->ship_method)->select('EnName')->first();
									if($shipping) {
										$shippinginfo = $shipping->EnName;
									}
								@endphp								
								<table>
									<tr><td>Date: {{ date('d/m/Y', strtotime($orders->date_entered)) }}</td></tr>
									<tr><td>Status: @if($orders->order_status == 0) Payment Pending @elseif($orders->order_status == 1) Paid, Shipping Pending @elseif($orders->order_status == 2) Shipped @elseif($orders->order_status == 3) Shipped @elseif($orders->order_status == 4) Delivered @elseif($orders->order_status == 5) On The Way To You @elseif($orders->order_status == 6) Partially Delivered @elseif($orders->order_status == 7) Partially Refunded @elseif($orders->order_status == 8) Fully Refunded @elseif($orders->order_status == 9) Ready For Collection @endif</td></tr>
									<tr><td>Delivery Method: {{ $shippinginfo }}</td></tr>
									@if(isset($orderdeliveryinfo->ship_tracking_number) && $orderdeliveryinfo->ship_tracking_number !='')
									<tr><td>Tracking NO.: {{ $orderdeliveryinfo->ship_tracking_number }}</td></tr>
									@endif
									<tr><td>If item(s) unavailable: @if($orders->if_items_unavailabel == 1) Call Me @elseif($orders->if_items_unavailabel == 2) Do Not Replace @else Replace @endif</td></tr>									
								</table>
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td width="65%" style="vertical-align:top;">
								<b>Shipping Info</b>
								<table>
									@if(stripos($shippinginfo, 'Self Collect') !== false)
										<tr><td>{{ $shippinginfo }}</td></tr>
									@else
										<tr><td>{{ $orders->ship_fname.' '.$orders->ship_lname }}</td></tr>
										<tr><td>{{ $orders->ship_ads1 }}</td></tr>
										@if($orders->ship_ads2 != '')
										<tr><td>{{ $orders->ship_ads2 }}</td></tr>
										@endif
										<tr><td>{{ $orders->ship_city }}</td></tr>
										<tr><td>{{ $orders->ship_state }}</td></tr>
										@if(!empty($shipCountryData))
    									<tr><td>{{ $shipCountryData->countryname.' - '.$orders->ship_zip }}</td></tr>
    									@else
    									<tr><td>{{ ' - '.$orders->ship_zip }}</td></tr>
    									@endif
										<tr><td style="white-space:nowrap;">Email: {{ $orders->ship_email }}</td></tr>
										<tr><td>Mobile: {{ $orders->ship_mobile }}</td></tr>
									@endif
								</table>
							</td>
							<td width="35%">
								<b>Billing Info</b>
								<table>
								    @if(!empty($orders->bill_compname))
								    <tr><td>{{ $orders->bill_compname }}</td></tr>
								    @endif
									<tr><td>{{ $orders->bill_fname.' '.$orders->bill_lname }}</td></tr>
									
									<tr><td>{{ $orders->bill_ads1 }}</td></tr>
									@if($orders->bill_ads2 != '')
									<tr><td>{{ $orders->bill_ads2 }}</td></tr>
									@endif
									<tr><td>{{ $orders->bill_city }}</td></tr>
									<tr><td>{{ $orders->bill_state }}</td></tr>
									@if(!empty($billCountryData))
									<tr><td>{{ $billCountryData->countryname.' - '.$orders->bill_zip }}</td></tr>
									@else
									<tr><td>{{ ' - '.$orders->bill_zip }}</td></tr>
									@endif
									<tr><td style="white-space:nowrap;">Email: {{ $orders->bill_email }}</td></tr>
									<tr><td>Mobile: {{ $orders->bill_mobile }}</td></tr>
								</table>
							</td>
						</tr>
						@if($orders->pay_method != '' || !empty($orders->delivery_instructions))
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td>
						    @if($orders->pay_method != '')
							<b>Payment Method</b>
							
							<table>
								<tr><td>{{ $orders->pay_method }}</td></tr>
								
							</table>
							@endif
						</td><td>
						@if($orders->delivery_instructions != '')
							<b>Notes</b>
							
							<table>
								<tr><td>{{$orders->delivery_instructions }}</td></tr>
								
							</table>
							@endif
						</td></tr>
						@endif
						<tr class="print-hide"><td colspan="2">&nbsp;</td></tr>
						<tr class="print-hide"><td colspan="2"><a href="javascript:(0);" class="btn btn-primary" id="addNewItem">Add New Item</a></td></tr>
						<tr style="display:none;" class="newItemBlocks print-hide"><td colspan="2">&nbsp;</td></tr>
						<tr style="display:none;" class="newItemBlocks print-hide">
							<td colspan="2">
								<table class="table-bordered" width="100%" cellpadding="3">
									<thead>
										<tr>
											<th>&nbsp;</th>
											<th>Item</th>
											<th>Variant</th>
											<th>Quantity</th>
											<th>Total Price</th>
										</tr>
									</thead>
									<tbody id="newItemsBody"></tbody>
								</table>
							</td>
						</tr>
						<tr style="display:none;" class="newItemBlocks print-hide"><td colspan="2">&nbsp;</td></tr>
						<tr style="display:none;" class="newItemBlocks print-hide">
							<td colspan="2">
								<a href="javascript:(0);" class="btn btn-primary" id="saveNewItems">Save</a>
								<a href="javascript:(0);" class="btn btn-outline-warning" id="clearNewItems">Cancel</a>
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td colspan="2">
								<table class="table-bordered" width="100%" cellpadding="8">
									<tr >
										<!--<th> &nbsp; </th>-->
										<th width="35%">Item</th>
										<th width="10%" style="text-align:center;">Quantity</th>
										<th width="10%" style="text-align:right">Price</th>
										<th width="20%" style="text-align:right">Status</th>
										<th width="10%" style="text-align:right">Total</th>
										<th width="15%" align="center" class="print-hide">Actions</th>
									</tr>
									@if($orderdetails)
										@foreach($orderdetails as $orderdetail)	
											@php
												$product = \App\Models\Product::where('Id', '=', $orderdetail->prod_id)->first();
												$totalAmt = number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2);
												$productOptions = \App\Models\ProductOptions::where([['Prod', '=', $orderdetail->prod_id],['Status','=',1]])->get();
											@endphp
											<tr id="item_row_{{ $orderdetail->detail_id }}">
												<td>{{ $orderdetail->prod_name }}<br>
												@if($orderdetail->prod_option != '')
													Option: <span class="ed_hd_{{ $orderdetail->detail_id }}">{{ $orderdetail->prod_option }}</span>
													<select id="var_{{ $orderdetail->detail_id }}" class="ed_sh_{{ $orderdetail->detail_id }} w-50 form-control" style="display:none;">
													    @foreach($productOptions as $pOpt)
													    <option @if($orderdetail->prod_option==$pOpt->Title) selected @endif value="{{ $pOpt->Id }}">{{ $pOpt->Title }}</option>
													    @endforeach
													</select>
													<br>
												@endif

												</td>
												<td align="center">
												    <span class="ed_hd_{{ $orderdetail->detail_id }}">{{ $orderdetail->prod_quantity }}</span>
												    <input id="quantity_{{ $orderdetail->detail_id }}" type="number" min="1" class="form-control ed_sh_{{ $orderdetail->detail_id }} w-50 qfloat-right validNum sv_{{ $orderdetail->detail_id }}" value="{{ $orderdetail->prod_quantity }}" style="display:none;" />
												</td>
												<td style="text-align:right">
												    <span class="ed_hd_{{ $orderdetail->detail_id }}">S${{ $orderdetail->prod_unit_price }}</span>
												    <input id="unit_price_{{ $orderdetail->detail_id }}" type="number" min="1" class="form-control ed_sh_{{ $orderdetail->detail_id }} w-100 float-right validNum sv_{{ $orderdetail->detail_id }}" value="{{ $orderdetail->prod_unit_price }}" style="display:none;" />
												</td>
												<td style="text-align:right">
												@php
													$delivered = $refunded = 0;
													$deliverydetails = \App\Models\OrderDeliveryDetails::where('order_id', '=', $orders->order_id)->where('prod_id', '=', $orderdetail->prod_id)->where('detail_id', '=', $orderdetail->detail_id)->get();
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
												<td style="text-align:right">
												    S${{ $totalAmt }}
												</td>
												<td style="font-size:1.7rem !important;" class="print-hide">
												    <a href="javascript:void(0);" title="Edit" data-row-type="item" class="text-info editRows ed_row_{{ $orderdetail->detail_id }}" data-row-id="{{ $orderdetail->detail_id }}"><i class="fa fa-pencil fa-fw"></i></a>
												    <a href="javascript:void(0);" class="text-success ed_sh_{{ $orderdetail->detail_id }} saveRow" data-row-id="{{ $orderdetail->detail_id }}" title="Update" style="display:none"><i class="fa fa-check fa-fw"></i></a>
												    <a href="" class="text-grey ed_sh_{{ $orderdetail->detail_id }}" data-row-id="{{ $orderdetail->detail_id }}" title="Cancel edit" style="display:none"><i class="fa fa-close fa-fw"></i></a>
												    <a href="javascript:void(0);" data-row-id="{{ $orderdetail->detail_id }}" class="text-danger deleteRow" title="Delete"><i class="fa fa-trash fa-fw"></i></a>
												</td>
											</tr>											
										@endforeach
									@endif
									<tr style="display:none" id="editUpdate">
										<td colspan="5">
											<a href="javascript:(0);" class="btn btn-primary" id="removeSelectedItem">Remove selected item(s)</a>
											<a href="javascript:(0);" class="btn btn-outline-warning" id="clearSelectedItem">Cancel</a>
										</td>
									</tr>
									<tr><td colspan="4">&nbsp;</td></tr>
									<tr>
										<td colspan="2"></td><td colspan="2">Sub Total</td>
										<td style="text-align:right">S${{ number_format(($orders->payable_amount + $orders->discount_amount) - ($orders->shipping_cost + $orders->packaging_fee + $orders->tax_collected + $orders->fuelcharges + $orders->handlingfee), 2) }}
										</td>
									</tr>
									<tr>
										<td colspan="2"></td><td colspan="2">{{($orders->tax_label !='')?$orders->tax_label:'Tax'}}</td>
										<td style="text-align:right">
										<span class="ed_trow_{{ $orderdetail->order_id }}">S${{ number_format($orders->tax_collected, 2) }}</span>
										<input id="tax_total_{{ $orderdetail->order_id }}" data-allow-zero="1" type="number" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum" value="{{ $orders->tax_collected }}" style="display:none;" />
										</td>
									</tr>
									<tr>
										<td colspan="2"></td><td colspan="2">Shipping</td>
										<td style="text-align:right">
										<span class="ed_trow_{{ $orderdetail->order_id }}">S${{ number_format($orders->shipping_cost, 2) }}</span>
										<input id="shipping_total_{{ $orderdetail->order_id }}" data-allow-zero="1" type="number" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum" value="{{ $orders->shipping_cost }}" style="display:none;" />
										</td>
									</tr>
									@if($orders->fuelcharges >0 )
									<tr>
										<td colspan="2"></td><td colspan="2">{{(int)$orders->fuelcharge_percentage}}% Fuel Charges</td>
										<td style="text-align:right">
										<span class="ed_trow_{{ $orderdetail->order_id }}">S${{ number_format($orders->fuelcharges, 2) }}</span>
										<input id="fuelcharges_total_{{ $orderdetail->order_id }}" data-allow-zero="1" type="number" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum" value="{{ $orders->fuelcharges }}" style="display:none;" />
										</td>
									</tr>
									@endif
									@if($orders->handlingfee >0 )
									<tr>
										<td colspan="2"></td><td colspan="2">Handling Fee</td>
										<td style="text-align:right">
										<span class="ed_trow_{{ $orderdetail->order_id }}">S${{ number_format($orders->handlingfee, 2) }}</span>
										<input id="handlingfee_total_{{ $orderdetail->order_id }}" data-allow-zero="1" type="number" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum" value="{{ $orders->handlingfee }}" style="display:none;" />
										</td>
										</td>
									</tr>
									
									@endif
									<tr>
										<td colspan="2"></td><td colspan="2">Packaging Fee</td>
										<td style="text-align:right">
										<span class="ed_trow_{{ $orderdetail->order_id }}">S${{ number_format($orders->packaging_fee, 2) }}</span>
										<input id="packaging_fee_total_{{ $orderdetail->order_id }}" data-allow-zero="1" type="number" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum" value="{{ $orders->packaging_fee }}" style="display:none;" />
										</td>
									</tr>
									@if($orders->discount_id > 0)
                						@php
                							$coupondata = [];
                							$coupondata = \App\Models\Couponcode::where('id', '=', $orders->discount_id)->first();
                						@endphp
                						
                						@if($coupondata)
                						    <tr>
                							 <td colspan="2"></td><td colspan="2">Coupon discount({{ ($coupondata->discount_type == 1 ? $coupondata->discount.'%' : 'S$'.$coupondata->discount) }})</td>
        									 <td style="text-align:right">
        									 <span class="ed_trow_{{ $orderdetail->order_id }}">S${{ number_format($orders->discount_amount, 2) }}</span>
        									 <input id="discount_amount_total_{{ $orderdetail->order_id }}" type="number" data-allow-zero="1" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum" value="{{ $orders->discount_amount }}" style="display:none;" />
										</td>
        									 </td>
        									</tr>
                						@endif	
                					@endif
									<tr>
										<td colspan="2"></td><td colspan="2"><b>Grand Total</b></td>
										<td style="text-align:right">
										<span class="ed_trow_{{ $orderdetail->order_id }} text-bold">S${{ number_format($orders->payable_amount, 2) }}</span>
										<input id="payable_amount_total_{{ $orderdetail->order_id }}" type="number" min="0" class="form-control sh_trow_{{ $orderdetail->order_id }} w-100 float-right validNum tv_{{ $orderdetail->order_id }}" value="{{ $orders->payable_amount }}" style="display:none;" />
										</td>
										<td align="center" style="font-size:1.7rem !important;" class="print-hide">
										    <a href="javascript:void(0);" title="Edit amount" class="text-info editRows ed_trow_{{ $orderdetail->order_id }}" data-row-type="total" data-row-id="{{ $orderdetail->order_id }}"><i class="fa fa-pencil fa-fw"></i></a>
										    <a href="javascript:void(0);" title="Update" class="text-info saveTRow sh_trow_{{ $orderdetail->order_id }}" data-row-id="{{ $orderdetail->order_id }}" style="display:none"><i class="fa fa-check fa-fw"></i></a>
										    <a href="" title="Cancel edit" class="text-grey sh_trow_{{ $orderdetail->order_id }}" data-row-id="{{ $orderdetail->order_id }}" title="Cancel edit" style="display:none"><i class="fa fa-close fa-fw"></i></a>
										</td>
									</tr>
								</table>
							</td>
							
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td colspan="2">
							Thank and regards,<br>HardwareCity (S) Pte Ltd.
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
<script>
	let totalItems = parseInt('{{ count($orderdetails) }}');
	let removeItems = [];
	let newItems = [];
	let itemRow = 1;
	let itemVariantOptions = '';
	function removeValues(checkBoxId){
		let itemIndex = removeItems.indexOf(checkBoxId);
		if (itemIndex > -1) removeItems.splice(itemIndex, 1);
	}

	function calculatePrice(prodData){
	    $.ajax({
            url:"{{ url('/setOptionPrice') }}",
    		method:'POST',
    		data : prodData,
    		success: function(r){
    		    let result = r.split("#");
    		    let totalPrice = (prodData.quantity*result[0]).toFixed(2);
    		    $('body #quantity_price_'+prodData.rowId).val(result[0]);
    		    $('body #quantity_'+prodData.rowId).attr({'data-single-item':1,'data-single-price':result[0]});
    		    $('body #item_total_'+prodData.rowId).html('$'+totalPrice);
    		}
    	});
	}
	

	$(document).ready(function(e){
	    
	    $('.deleteRow').click(function(e){
	       
	       if(confirm('Do you want to remove the item?')){
	           let rowId=$(this).data('row-id');
	           $.ajax({
					url:"{{ url('admin/remove-order-items') }}",
					method:'POST',
					data : {
						order_id : '{{ $orders->order_id }}',
						items : [rowId],
						_token : '{{ csrf_token() }}'
					},
					success: function(result){
						alert(result.message);
						if(result.status) window.location.reload();
					}
				});
	       }
	        
	    });
	    
	    $('.saveTRow').click(function(e){
	       let rowId=$(this).data('row-id');
	       
	       
	       let isEmptyField = 0;
	       $('.tv_'+rowId).each(function(v){
	          if($(this).val()=='') isEmptyField++;
	       });
	       
	       if(isEmptyField>0){
	           alert('Fields are required');
	       }else{
	           
	           let disAmt = $('#discount_amount_total_'+rowId).length>0 ? $('#discount_amount_total_'+rowId).val() : 0;
	           let handlingAmt = $('#handlingfee_total_'+rowId).length>0 ? $('#handlingfee_total_'+rowId).val() : 0;
	           let fuelAmt = $('#fuelcharges_total_'+rowId).length>0 ? $('#fuelcharges_total_'+rowId).val() : 0;
	           
	           $.ajax({
		        url:"{{ url('admin/update_order_item') }}",
				method:'POST',
				data : {
				    action : 'update_total',
					order_id : '{{ $orders->order_id }}',
					tax : $('#tax_total_'+rowId).val(),
					shipping : $('#shipping_total_'+rowId).val(),
					packaging : $('#packaging_fee_total_'+rowId).val(),
					discount : disAmt,
					handling : handlingAmt,
					fuel : fuelAmt,
					tot_price : $('#payable_amount_total_'+rowId).val(),
					_token : '{{ csrf_token() }}'
				},
				success: function(r){
				    alert(r.message);
				    if(r.status==true) window.location.reload();
				    $('.ed_trow_'+rowId).show();
            	   $('.sh_trow_'+rowId).hide();
            	   $(this).hide();
			    }
    			});
	       }
	    });
	    
	    $('.validNum').keyup(function(e){
	       let inp = $(this).val();
	       let replaceValue = '';
	       if($(this).attr('data-allow-zero') && $(this).attr('data-allow-zero')=='1') replaceValue = 0;
	       if(inp=='') $(this).val(replaceValue);
	    });
	    
	    $('.saveRow').click(function(e){
	       let rowId=$(this).data('row-id');
	       
	       
	       let isEmptyField = 0;
	       $('.sv_'+rowId).each(function(v){
	          if($(this).val()=='') isEmptyField++;
	       });
	       
	       if(isEmptyField>0){
	           alert('Fields are required');
	       }else{
	           let variantId = $('#var_'+rowId).length ? $('#var_'+rowId).val() : 0;
	           $.ajax({
		        url:"{{ url('admin/update_order_item') }}",
				method:'POST',
				data : {
				    action : 'update_item',
					order_id : '{{ $orders->order_id }}',
					detail_id : rowId,
					quantity : $('#quantity_'+rowId).val(),
					price : $('#unit_price_'+rowId).val(),
					variant_id : variantId,
					_token : '{{ csrf_token() }}'
				},
				success: function(r){
				    alert(r.message);
				    if(r.status==true) window.location.reload();
				    $('.ed_hd_'+rowId).show();
        	       $('.ed_row_'+rowId).show();
        	       $('.ed_sh_'+rowId).hide();
        	       $(this).hide();
				}
    			});
	       }
	       
	    });
	    
	    $('.editRow').click(function(e){
	       let rowId=$(this).data('row-id');
	       $('.ed_hd_'+rowId).hide();
	       $('.ed_sh_'+rowId).show();
	       $(this).hide();
	    });
	    
	    $('.editRows').click(function(e){
	       let rowId= $(this).data('row-id');
	       let rowType = $(this).data('row-type');
	       let hideItems = '.ed_hd_'+rowId;
	       let showItem = '.ed_sh_'+rowId;
	       if(rowType=='total'){
	           hideItems = '.ed_trow_'+rowId;
	           showItem = '.sh_trow_'+rowId;
	       }
	       
	       $(hideItems).hide();
	       $(showItem).show();
	       $(this).hide();
	    });
	    
	    $('#saveNewItems').click(function(e){
	        
	        newItems = [];
	        let totalItemAmount = 0;
	       
            for(let i=1;i<=itemRow;i++){
                if($('body #item_'+i).length>0 && $('body #item_'+i).val()!=''){
                    totalItemAmount+= ($('body #quantity_'+i).val()*$('body #quantity_price_'+i).val());
                    newItems.push({
                        product_id : $('body #item_'+i).val(),
                        variant_id : $('body #item_variant_'+i).val(),
                        quantity : $('body #quantity_'+i).val(),
                        unit_price : $('body #quantity_price_'+i).val()
                    });
                }
            }
	            

	        if(newItems.length>0)
	        {
	            $.ajax({
    		        url:"{{ url('admin/add_more_items_to_order') }}",
    				method:'POST',
    				data : {
    				    order_id : '{{ $orders->order_id }}',
    	                items : newItems,
    	                total_amount : totalItemAmount,
    	                _token : '{{ csrf_token() }}'
    	            },
    				success: function(result){
    				    alert('Successfully order has been updated.');
    				    window.location.reload();
    				}
	            });
	        }else{
	            alert('Please add at least one item.');
	        }
	    });
	    
	    
	    $('body').on('change','.addProdDrop', function(e){
	        let rowId = $(this).data('row');
	        let productId = $(this).val();
	        
	        if(productId!=''){
	            
	           let selectedProdPrice = $(this).find("option:selected").attr("data-prod-price");
	           
    	       $.ajax({
		        url:"{{ url('admin/fetch_otions_and_price') }}",
				method:'POST',
				data : {
					product_id : productId,
					_token : '{{ csrf_token() }}'
				},
				success: function(result){
				    let vOptions = '<option value="">--select--</option>';
				    (result.data.variants).forEach(function(v){
				        vOptions +='<option value="'+v.Id+'">'+v.Title+'</option>'; 
				    });
				    $('body #item_variant_'+rowId).html(vOptions);
				    $('body #item_variant_'+rowId).attr('data-prod-price',selectedProdPrice);
				    $('body #quantity_'+rowId).val(1);
				    $('body #quantity_'+rowId).attr({'data-single-item':0,'data-single-price':0});
				    $('body #item_total_'+rowId).empty();
				    $('body #quantity_price_'+rowId).val(0);
				    if(result.data.variants.length==0){
				      $('body #quantity_'+rowId).attr({'data-single-item':1,'data-single-price':result.data.item_price});
				      $('body #quantity_'+rowId).val(1);
    		          $('body #quantity_price_'+rowId).val(result.data.item_price);
				      $('body #item_total_'+rowId).html('S$'+result.data.item_price);  
				    }
				}
    			});
	        }else{
	            $('body #quantity_'+rowId).attr({'data-single-item':1,'data-single-price':0});
	            $('body #quantity_'+rowId).val(1);
	            $('body #item_variant_'+rowId).html('<option value="">--select--</option>');
	            $('body #item_total_'+rowId).empty();
	            $('body #item_variant_'+rowId).attr('data-prod-price',0);
	            $('body #quantity_price_'+rowId).val(0);
	        }
	    });
	    
	    $('body').on('change','.itemVariantChange', function(e){
	        if(($(this).val())!=''){
	           let rowId = $(this).data('item-row');
	           let prodData = {
    		        prodid : $('body #item_'+rowId).val(),
    			    optionid : $(this).val(),
    			    displayprice : $(this).data('prod-price'),
    				_token : '{{ csrf_token() }}',
    				quantity:$('body #quantity_'+rowId).val(),
    				rowId : rowId
    		    };
	            if(prodData.prodid!='') calculatePrice(prodData);
	        }else{
	            $('body #item_total_'+rowId).empty();
	        }
	    });
	    
	    $('body').on('keyup','.quantityChange', function(e){
	        let quantity = $.trim($(this).val());
	        let rowId = $(this).data('item-row');
	        if(quantity!=''){
	            if(quantity==0){
	                quantity=1;
	                $(this).val(quantity);
	            }
	        }else{
	            quantity=1;
	            $(this).val(quantity);
	        }
	        let totalPrice = (quantity*$(this).attr('data-single-price')).toFixed(2);
    		$('body #quantity_price_'+rowId).val($(this).attr('data-single-price'));
	        $('body #item_total_'+rowId).html($(this).attr('data-single-price')!=0 ? 'S$'+totalPrice : '');
	    });

		//Add new items
		$('#addNewItem').click(function(e){
			$('.newItemBlocks').show();
			let contents = '<tr id="new_item_'+itemRow+'" class="new_item_rows">';
			contents+= '<td><a href="javascript:(0);" class="text-danger removeNewItem" data-item-row="'+itemRow+'"><i class="fa fa-trash"></i></a></td>';
			contents+= '<td class="w-50"><select class="form-control addProdDrop" data-row="'+itemRow+'" id="item_'+itemRow+'" name="item_'+itemRow+'"></select></td>';
			contents+= '<td><select class="form-control itemVariantChange" data-prod-price="0" data-item-row="'+itemRow+'" id="item_variant_'+itemRow+'" name="item_variant_'+itemRow+'"><option value="">--select--</option>'+itemVariantOptions+'</select></td>';
			contents+= '<td><input type="number" value="1" data-single-item="1" data-single-price="0" min="1" class="form-control w-50 quantityChange" data-item-row="'+itemRow+'" id="quantity_'+itemRow+'" name="quantity_'+itemRow+'"></td>';
			contents+= '<td><span id="item_total_'+itemRow+'"></span><input type="hidden" value="0" id="quantity_price_'+itemRow+'" name="quantity_price_'+itemRow+'"></td>';
			contents+= '</tr>';
            
			$('#newItemsBody').append(contents);
			
			$('#item_'+itemRow).select2({
              ajax: {
                url: "{{ url('admin/search/prods') }}",
                dataType: 'json'
              }
            });
			itemRow++;
		});
		
		$('body').on('click','.removeNewItem', function(e){
			$('#new_item_'+$(this).data('item-row')).remove();
			if($('.removeNewItem').length==0) $('.newItemBlocks').hide();
		});

		$('#clearNewItems').on('click', function(){			
			newItems = [];
			$(".new_item_rows").remove();
			$('.newItemBlocks').hide();
		});

		//End
		
		//Remove existing items - Start
		$('#removeSelectedItem').on('click', function(){
			if(removeItems.length==0){
				alert('Please select an item');
			}else{
				$.ajax({
					url:"{{ url('admin/remove-order-items') }}",
					method:'POST',
					data : {
						order_id : '{{ $orders->order_id }}',
						items : removeItems,
						_token : '{{ csrf_token() }}'
					},
					success: function(result){
						alert(result.message);
						if(result.status) window.location.reload();
					}
				});
			}
		});

		$('#clearSelectedItem').on('click', function(){			
			removeItems = [];
			$("#editUpdate").hide();
			$('.removeItem').each(function(e){
				if($(this).is(':checked')) $(this).prop("checked", false);
			});
		});

		$('.removeItem').change(function(){

			if($(this).is(':checked')){
				if(!removeItems.includes($(this).val())) removeItems.push($(this).val());
			}else{
				removeValues($(this).val());
			}
			if(removeItems.length==0){
				$("#editUpdate") . hide();
			}else{

    			if(removeItems.length == totalItems){
					removeValues($(this).val());
    				$(this).prop("checked", false);
    				alert("Can't remove all items");
    			}else{
    			    $("#editUpdate") . show();
    			}
			}
		});
		//End

		
	})
</script>
