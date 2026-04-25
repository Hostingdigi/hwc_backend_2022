@include('admin.includes.header')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->


<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:100%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 text-center">
                            <h2 class="content-header-title">Manage Orders</h2>                            
                        </div>
						<!--div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/country/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div-->
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
				<form name="order_filter_frm" id="order_filter_frm" action="{{ url('/admin/orders') }}" method="GET">
				
				<div class="col-md-8 col-8 col-md-offset-4" style="margin:0 auto;">
					<div class="card">
						
						<div class="card-content">
							<div class="card-body">
								<form class="form form-horizontal">
									<div class="form-body">
										<div class="row">
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Status</span>
													</div>
													<div class="col-md-8">
														<select class="form-control" name="order_status" id="order_status">
															<option value = "" @if($orderstatus == '') selected @endif>Show All Orders</option>
															<option value = "1" @if($orderstatus == '1') selected @endif>Payment Pending</option>
															<option value = "2" @if($orderstatus == '2') selected @endif>Paid,Shipment Pending</option>
															<option value = "3" @if($orderstatus == '3') selected @endif>Shipped</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Order Type</span>
													</div>
													<div class="col-md-8">
													<select name="order_type" class="form-control" id="order_type">	
														<option value="" @if($ordertype == '') selected @endif>Show All</option>
														<option value="1" @if($ordertype == '1') selected @endif>Online Order</option>
														<option value="2" @if($ordertype == '2') selected @endif>Quotation</option>
													</select>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Order Date</span>
													</div>
													<div class="col-md-4">
														From
														<input type="text" id="orderfrom" class="form-control pickadate" name="orderfrom" value="{{ $orderfrom }}">
													</div>
													<div class="col-md-4">
														To
														<input type="text" id="orderto" class="form-control pickadate" name="orderto" value="{{ $orderto }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Order Number</span>
													</div>
													<div class="col-md-4">
														From
														<input type="text" id="start_order_num" class="form-control" name="start_order_num" value="{{ $orderstartno }}">
													</div>
													<div class="col-md-4">
														To
														<input type="text" id="end_order_num" class="form-control" name="end_order_num" value="{{ $orderendno }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Filter</span>
													</div>
													<div class="col-md-2">
													<select class="form-control" name="filter_column" id="filter_column">			
															<option value = "cust_firstname" @if($filtercolumn == 'cust_firstname') selected @endif>First Name</option>
															<option value = "cust_lastname" @if($filtercolumn == 'cust_lastname') selected @endif>Last Name</option>			
													</select>
													</div>
													<div class="col-md-2">
													<select class="form-control" name="filter_srch_type" id="filter_srch_type">		
															<option value = "1" @if($filter_srch_type == '1') selected @endif>Equals</option>
															<option value = "2" @if($filter_srch_type == '2') selected @endif>Contains</option>			
													</select>
													</div>
													<div class="col-md-4">
													<input type="text" id="filter_srch_val" class="form-control" name="filter_srch_val" placeholder="Value" value="{{ $filter_srch_val }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Sort By</span>
													</div>
													<div class="col-md-8">
													<select class="form-control" name="sort_column" id="sort_column">
															<option value = "0" @if($sortcolumn == '0') selected @endif>Default</option>
															<option value = "1" @if($sortcolumn == '1') selected @endif>Order Date Ascending</option>
															<option value = "2" @if($sortcolumn == '2') selected @endif>Order Date Descending</option>
															<option value = "3" @if($sortcolumn == '3') selected @endif>First Name Ascending</option>
															<option value = "4" @if($sortcolumn == '4') selected @endif>First Name Descending</option>
															<option value = "5" @if($sortcolumn == '5') selected @endif>Last Name Ascending</option>
															<option value = "6" @if($sortcolumn == '6') selected @endif>Last Name Descending</option>
													</select>
													</div>
												</div>
											</div>
											<div class="col-md-8 offset-md-4">
												<button type="submit" class="btn btn-primary mr-1 mb-1">Search</button>
												<button type="button" id="reset" class="btn btn-outline-warning mr-1 mb-1">Clear</button>
												
												@if(in_array('21_Export CSV', $moduleaccess) || $adminrole == 0)
												<a href="{{ url('/admin/exportorders?order_status='.$orderstatus.'&order_type='.$ordertype.'&orderfrom='.$orderfrom.'&orderto='.$orderto.'&start_order_num='.$orderstartno.'&end_order_num='.$orderendno.'&filter_column='.$filtercolumn.'&filter_srch_type='.$filter_srch_type.'&filter_srch_val='.$filter_srch_val.'&sort_column='.$sortcolumn) }}" id="export" class="btn btn-primary mr-1 mb-1" target="_blank">Export CSV</a>
												@endif
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				</form>
				
				<form class="form form-horizontal" name="order_frm" id="order_frm" method = "post" action="{{ url('/admin/orders/bulkaction') }}" enctype="multipart/form-data">                                            
				{{ csrf_field() }}
				
                <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>Select</th>
                                    <th>Order Id</th>                                                                        
                                    <th>Date</th>
									<th>Customer Name</th>
									<th>Amount</th>
									<th>Invoice Type</th>
									<th>Delivery In</th>
									<th>Status</th>
									<th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($orders) > 0)
                               	@foreach($orders as $order)
                                    <tr>
											<td><input type="checkbox" value="{{ $order->order_id }}" name="orderids[]"></td>
                                        	<td>
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
												
												$deliveryin = $collectstatus = '';
												$delivery = \App\Models\ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
												if($delivery) {
													if(strpos($delivery->EnName, 'Self Collection') !== false) {
														$deliveryin = 'Self Collection';
													}
												}
																									
												$selfcollection = \App\Models\SelfCollectionInfo::where('order_id', '=', $order->order_id)->select('collection_status')->first();
												
												if($selfcollection) {
													$collectstatus = $selfcollection->collection_status;
												}
												
											@endphp
											
											{{ $order->order_id }}
											</td>
											<td>{{ date('d M Y h:i A', strtotime($order->date_entered)) }}</td>
											<td>{{ $order->bill_fname }} {{ $order->bill_lname }}</td>
											<td>${{ $order->payable_amount }}</td>
											<td>
												@if($order->order_type == "1")
													Online Order
												@else
													Quotation
												@endif
											</td>
											<td>@if($deliveryin != '') {{ $deliveryin }} @elseif($order->delivery_times == 1) Single Delivery @else Multiple Delivery @endif</td>
											<td>@if($order->order_status == 0) Payment Pending @elseif($order->order_status == 1) Paid, Shipping Pending @elseif($order->order_status == 2) Shipped @elseif($order->order_status == 3) Shipped @elseif($order->order_status == 5) On the Way To You @elseif($order->order_status == 6) Partially Delivered @elseif($order->order_status == 7) Partially Refund @elseif($order->order_status == 8) Fully Refund @elseif($order->order_status == 9) Ready For Collection @endif</td>
											<td align="center">
											@if(in_array('21_View', $moduleaccess) || $adminrole == 0)
											<a href="{{ url('/admin/orders/'.$order->order_id) }}"><i class="fa fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
											@endif

											@if(in_array('21_Edit', $moduleaccess) || $adminrole == 0)
												@if($order->order_type == 1)
													@if($deliveryin != '')
														<a href="{{ url('/admin/orders/'.$order->order_id.'/selfcollect') }}"><i class="fa fa-shopping-bag fa-lg" aria-hidden="true"></i></a>
													@elseif($order->delivery_times == 1)
														@if($order->order_status == 2)
															<a href="{{ url('/admin/orders/'.$order->order_id.'/deliverytrack') }}"><i class="fa fa-truck fa-lg" aria-hidden="true"></i></a>
														@else	
															<a href="{{ url('/admin/orders/'.$order->order_id.'/singledelivery') }}"><i class="fa fa-truck fa-lg" aria-hidden="true"></i></a>
														@endif
													@else
														<a href="{{ url('/admin/orders/'.$order->order_id.'/multipledelivery') }}"><i class="fa fa-truck fa-lg" aria-hidden="true"></i></a>
													@endif
												@else
													<a href="javascript:void(0);"><i class="fa fa-truck fa-lg" aria-hidden="true" style="color:#CCC;"></i></a>
												@endif
											@endif	
											
											@if(in_array('21_Delete', $moduleaccess) || $adminrole == 0)
											&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="chkdelete({{ $order->order_id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
											@endif
											</td>
                                    </tr>
                                @endforeach
							@else
								<tr><td colspan="9" align="center">No Records Found</td></tr>
							@endif
                            </tbody>
                            </table>
                </div>
				<div class="row" style="padding:10px 0;">
					<div class="col-md-10">
						@if ($orders->hasPages())
							<ul class="pagination pagination">
								{{-- Previous Page Link --}}
								@if ($orders->onFirstPage())
									<li class="disabled"><span>«</span></li>
								@else
									<li><a href="{{ $orders->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
								@endif

								@if($orders->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $orders->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($orders->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $orders->lastPage()) as $i)
									@if($i >= $orders->currentPage() - 2 && $i <= $orders->currentPage() + 2)
										@if ($i == $orders->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $orders->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($orders->currentPage() < $orders->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($orders->currentPage() < $orders->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $orders->appends($_GET)->url($orders->lastPage()) }}">{{ $orders->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($orders->hasMorePages())
									<li><a href="{{ $orders->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
								@else
									<li class="disabled"><span>»</span></li>
								@endif
							</ul>
						@endif
					</div>
					
					@if(in_array('21_Bulk Action', $moduleaccess) || $adminrole == 0)
					<div class="col-md-2" style="float:right; text-align:right; margin-bottom:20px;">
					<select name="bulk_action" id="bulk_action" class="form-control">
						<option value="">Select </option>
						<option value="single_delivery">Mark as Single Delivery</option>
						<option value="multiple_delivery">Mark as Multiple Delivery</option>
						<option value="on_the_way">Mark as On The Way To You</option>
						<option value="partial_delivered">Mark as Partial Delivered</option>
						<option value="partial_refunded">Mark as Partial Refunded</option>
						<option value="full_refunded">Mark as Full Refunded</option>
						<option value="shipped">Mark as Shipped / Delivered</option>
					</select>
					</div>
					@endif
				</div>
            </section>
            </div>
        </div>
</div>





    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<script type="text/javascript">
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this order?');
	if(chk) {
		window.location = "{{ url('/admin/orders') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this order?');
	if(chk) {
		window.location = "{{ url('/admin/orders') }}/"+id+"/"+status+"/updatestatus";
	}
}
</script>	
<script>
$(document).ready(function() {
	$('#reset').click(function() {
		window.location = "{{ url('/admin/orders') }}";
	});
		
	
	$('#bulk_action').change(function() {
		var action = $('#bulk_action').val();
		
		if(action != '') {
			var del_process = 0;
			for(i=0; i < window.document.order_frm.elements.length; i++)
			{		
				if(window.document.order_frm.elements[i].name == "orderids[]")
				{
					if(window.document.order_frm.elements[i].checked == true)
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
					window.document.order_frm.submit();
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
