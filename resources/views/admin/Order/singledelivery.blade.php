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
        <div class="content-wrapper" style="width:60%; margin:0 auto;"> 
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
                                        <form class="form form-horizontal" name="singledelivery_frm" id="singledelivery_frm" method = "post" action="{{ url('/admin/orders/updatesingledelivery') }}" enctype="multipart/form-data">    
											<input type="hidden" name="orderid" value="{{ $order->order_id}}">			
											{{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Invoice No <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
																@php
																	$orderid = $order->order_id;
																	if(strlen($orderid) == 3) {
																		$orderid = date('Ymd', strtotime($order->created_at)).'0'.$orderid;
																	} elseif(strlen($orderid) == 2) {
																		$orderid = date('Ymd', strtotime($order->created_at)).'00'.$orderid;
																	} elseif(strlen($orderid) == 1) {
																		$orderid = date('Ymd', strtotime($order->created_at)).'000'.$orderid;
																	} else {
																		$orderid = date('Ymd', strtotime($order->created_at)).$orderid;
																	}
																@endphp
                                                                <input type="text" id="invoiceno" class="form-control" name="invoiceno" value = "{{ $orderid }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>   
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Shipping By <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select name="shipping_by" class="form-control" id="shipping_by" required>
																<option value="">Select</option>
																<option value="Ninja Van">Ninja Van</option>
																<option value="HWC Own Feet"> HWC Own Feet</option>
																<option value="DHL">DHL</option>
																</select>   
                                                             </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Requested Tracking No <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="requested_tracking_number" class="form-control" name="requested_tracking_number" value = "{{ $trackingid }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12" id="ninjaform" style="display:none;">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Box Size</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select name="size" class="form-control">
																	<option value="S">S</option>
																	<option value="M">M</option>
																	<option value="L">L</option>
																</select>
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Weight</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="weight" class="form-control" name="weight" value="" >
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Address Line1 <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ship_ads1" class="form-control" name="ship_ads1" value="{{ $order->ship_ads1 }}" required>
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Address Line2</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ship_ads2" class="form-control" name="ship_ads2" value="{{ $order->ship_ads2 }}" >
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Country <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select name="ship_country" id="ship_country" class="form-control" required>
																	<option value=""></option>
																	@if($countries)
																		@foreach($countries as $country)
																			<option value="{{ $country->countryid }}" @if($country->countrycode == $order->ship_country) selected @endif>{{ $country->countryname }}</option>
																		@endforeach
																	@endif	
																</select>
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Postal Code <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ship_zip" class="form-control" name="ship_zip" value="{{ $order->ship_zip }}" required>
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Contact Number</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ship_mobile" class="form-control" name="ship_mobile" value="{{ $order->ship_mobile }}" >
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Pickup Instructions</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="pickup_instruction" class="form-control" name="pickup_instruction"></textarea>
                                                            </div>
                                                        </div>
														<div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Instructions <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="delivery_instructions" class="form-control" name="delivery_instructions"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Change Invoice Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select name="order_status" class="form-control">
																	<option value="2">Mark as Shipped</option>
																	<option value="1">Mark as Paid/ Shippmend pending</option>
																	<option value="0" selected>Mark as Not Paid</option>
																</select>  
                                                             </div>
                                                        </div>
                                                    </div>	
                                                    
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Save</button>
                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1" id="reset">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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
});
</script>
