@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
}
</style>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->
    <div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:85%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Order</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/orders/') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
						
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Update</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="multipledelivery_frm" id="multipledelivery_frm" method = "post" action="{{ url('/admin/orders/updatemultipledelivery') }}" enctype="multipart/form-data">    
											<input type="hidden" name="orderid" value="{{ $order->order_id}}">			
											{{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <table class="table table-striped table-bordered">
															<tr>
																<th>#</th>
																<th>Item</th>
																<th>Unit Price</th>
																<th>Total Qty</th>
																<th width="100">Status</th>
																<th>Ready to Ship / Refund</th>
															</tr>
															@if($orderdetails)
																@foreach($orderdetails as $orderdetail)
																@php
																	$remainingqty = 0;
																	$delrefundqty = 0;
																	$orddeldetails = \App\Models\OrderDeliveryDetails::where('order_id', '=', $orderdetail->order_id)->where('prod_id', '=', $orderdetail->prod_id)->select('quantity')->get();
																	if($orddeldetails) {
																		foreach($orddeldetails as $orddeldetail) {
																			$delrefundqty += $orddeldetail->quantity;
																		}
																	}
																	
																	$remainingqty = $orderdetail->prod_quantity - $delrefundqty;
																@endphp
																<tr @if($remainingqty == 0) style="background:#CCC;" @endif>
																	<td><input type="checkbox" value="{{ $orderdetail->prod_id }}" name="prodids[]" @if($remainingqty == 0) disabled @endif></td>
																	<td>{{ $orderdetail->prod_name }}</td>
																	<td>${{ $orderdetail->prod_unit_price }}</td>
																	<td>{{ $orderdetail->prod_quantity }}</td>
																	<td>Delivered: {{ $delrefundqty }}</td>
																	<td>
																	@if($remainingqty > 0)	
																		<input type="text" class="form-control" value="{{ $remainingqty }}" name="prod_id{{ $orderdetail->prod_id }}" style="width:100px;" max="{{ $orderdetail->prod_quantity }}">
																	@else
																		{{ $remainingqty }}
																	@endif	
																	</td>
																</tr>
																@endforeach
															@endif	
														</table>
                                                    </div>
                                                    <div class="col-md-3">
                                                        Next Delivery Date: <input type="text" name="next_delivery_date" class="form-control pickadate">
                                                    </div>
													<div class="col-md-6"></div>
													<div class="col-md-3">
                                                        Choose Your Action: <select name="bulk_action" id="bulk_action" class="form-control" >
														<option value="">Select </option>
														<option value="delivery">To Delivery</option>
														<option value="refund">To Refund</option>
														</select>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
										@if(count($orderdeliverydetails) > 0)
										<h1 align="center" style="margin-top:20px;">DELIVERY TRACKING RECORDS</h1>
										<div class="form-body">
											<div class="row">
												<div class="col-12">
													<table class="table table-striped table-bordered">
														<tr>
															<th>Date</th>
															<th>Items</th>
															<th>Status</th>
															<th>Next Delivery Date</th>
															<th>Tracking No</th>															
														</tr>
														@foreach($orderdeliverydetails as $orderdeliverydetail)
															<tr>
																<td>{{ date('d M Y', strtotime($orderdeliverydetail->created_at)) }}</td>
																<td>
																@php
																	$productname = '';
																	$productids = [];
																	$deldetails = \App\Models\OrderDeliveryDetails::where('delivery_info_id', '=', $orderdeliverydetail->delivery_info_id)->where('order_id', '=', $orderdeliverydetail->order_id)->get();
																	if($deldetails) {
																		foreach($deldetails as $deldetail) {
																			$orddetails = \App\Models\OrderDetails::where('prod_id', '=', $deldetail->prod_id)->select('prod_name')->get();
																			foreach($orddetails as $orddetail) {
																				if(!in_array($deldetail->prod_id, $productids)) {
																					echo $orddetail->prod_name.': '.$orderdeliverydetail->quantity."<br>";
																					$productids[] = $deldetail->prod_id;
																				}
																			}
																		}
																	}
																@endphp
																</td>
																<td>@if($orderdeliverydetail->status == 1) Delivered @else Refunded @endif</td>
																<td>
																@php
																	
																	$nextdeliverydate = $shippingby = '';
																	$deliveryinfo = \App\Models\OrderDeliveryInfo::where('delivery_id', '=', $orderdeliverydetail->delivery_info_id)->where('order_id', '=', $orderdeliverydetail->order_id)->select('next_delivery_date', 'shipping_by')->first();
																	if($deliveryinfo) {
																		$nextdeliverydate = $deliveryinfo->next_delivery_date;
																		$shippingby = $deliveryinfo->shipping_by;
																	}
																@endphp
																@if($nextdeliverydate != '' && $nextdeliverydate != '0000-00-00') 
																{{ date('d M Y', strtotime($nextdeliverydate)) }}
																@endif	
																</td>
																<td>
																@if($orderdeliverydetail->status == 1)
																	@if($shippingby == '')
																		<a href="{{ url('/admin/orders/'.$orderdeliverydetail->order_id.'/'.$orderdeliverydetail->delivery_info_id.'/deliveryinfo') }}"><i class="fa fa-truck fa-lg" aria-hidden="true"></i></a>
																	@else																	
																		<a href="{{ url('/admin/orders/'.$orderdeliverydetail->delivery_info_id.'/trackorder') }}" class="btn btn-primary mr-1 mb-1 waves-effect waves-light" target="_blank">Track Order</a>
																	@endif
																@endif	
																</td>
															</tr>
														@endforeach
													</table>
												</div>
											</div>
										</div>
										@endif
                                    </div>
                                </div>
                            </div>
							
                        </div>
                    </div>
                </section>


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')
<script type="text/javascript">
$(document).ready(function() {
	$('#shipping_by').change(function() {
		var shippingby = $('#shipping_by').val();
		if(shippingby != '') {
			if(shippingby == 'Ninja Van') {
				$('#ninjaform').show();
				$('#delivery_instructions').prop('required', 'required');
			} else {
				$('#ninjaform').hide();
				$('#delivery_instructions').prop('required', '');
			}
		}
	});
	
	$('#bulk_action').change(function() {
		var action = $('#bulk_action').val();
		
		if(action != '') {
			var del_process = 0;
			for(i=0; i < window.document.multipledelivery_frm.elements.length; i++)
			{		
				if(window.document.multipledelivery_frm.elements[i].name == "prodids[]")
				{
					if(window.document.multipledelivery_frm.elements[i].checked == true)
					{
						del_process = 1;
						break;
					}
				}
			}		
			
			if(del_process == 0)
			{
				alert("Select records to continue !!");
			}
			else
			{
				 doyou = confirm("Do you want to continue? (OK = Yes   Cancel = No)");
				 if (doyou == true)
				 {		  	
					window.document.multipledelivery_frm.submit();
				 }
			}
		}
	});
});
</script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>
$( function() {
    'use strict';
    $('.pickadate').pickadate({
        format: 'yyyy-mm-dd'

    });
});
</script>