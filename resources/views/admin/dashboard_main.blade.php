@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')

@php
	$overalltotal = $overallpaid = $overallpending = 0;
	$overallsales = \App\Models\OrderMaster::where('order_type', '=', '1')->get();
	if($overallsales) {
		foreach($overallsales as $overallsale) {
			$overalltotal = $overalltotal + $overallsale->payable_amount;
			if($overallsale->order_status == 0) {
				$overallpending = $overallpending + $overallsale->payable_amount;
			} else {
				$overallpaid = $overallpaid + $overallsale->payable_amount;
			}
		}
	}

	$salestotal = $paid = $pending = 0;
	$currentmonthstart = date('Y-m-01');
	$currentmonthend = date('Y-m-t');
	$thismonthsales = \App\Models\OrderMaster::whereBetween('date_entered', [$currentmonthstart, $currentmonthend])->where('order_type', '=', '1')->get();
	if($thismonthsales) {
		foreach($thismonthsales as $thismonthsale) {
			$salestotal = $salestotal + $thismonthsale->payable_amount;
			if($thismonthsale->order_status == 0) {
				$pending = $pending + $thismonthsale->payable_amount;
			} else {
				$paid = $paid + $thismonthsale->payable_amount;
			}
		}
	}
	
	$lastsalestotal = $lastpaid = $lastpending = 0;
	$lastmonthstart = date('Y-m-01', strtotime('last month'));
	$lastmonthend = date('Y-m-t', strtotime('last month'));
	$lastmonthsales = \App\Models\OrderMaster::whereBetween('date_entered', [$lastmonthstart, $lastmonthend])->where('order_type', '=', '1')->get();
	if($lastmonthsales) {
		foreach($lastmonthsales as $lastmonthsale) {
			$lastsalestotal = $lastsalestotal + $lastmonthsale->payable_amount;
			if($lastmonthsale->order_status == 0) {
				$lastpending = $lastpending + $lastmonthsale->payable_amount;
			} else {
				$lastpaid = $lastpaid + $lastmonthsale->payable_amount;
			}
		}
	}
	$paidpercentage = $pendingpercentage = 0;
	$recentorders = \App\Models\OrderMaster::orderBy('order_id', 'desc')->skip(0)->take(10)->get();
	if($overalltotal > 0) {
		$paidpercentage = number_format((($overallpaid / $overalltotal) * 100), 2);
		$pendingpercentage = number_format((($overallpending / $overalltotal) * 100), 2);
	}
@endphp
    <!-- BEGIN: Header-->
<div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    
                    <div class="row">
                        <div class="col-lg-8 col-md-6 col-12">
						
							<div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4 class="card-title">Overall Sales</h4>
                                    
                                </div>
                                <div class="card-content">
                                    <div class="card-body pb-0">
										<div class="row">
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Sales</p>
                                                <h2 class="text-bold-400">
                                                    <span class="text-success">${{ number_format($overalltotal, 2) }}</span>
                                                </h2>
											</div>
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Paid</p>
                                                <h2 class="text-bold-400">
                                                    <span>${{ number_format($overallpaid, 2) }}</span>
                                                </h2>
											</div>
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Pending</p>
                                                <h2 class="text-bold-400">                                                    
                                                    <span>${{ number_format($overallpending, 2) }}</span>
                                                </h2>
											</div>
										</div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
						
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4 class="card-title">This Month ({{ date('M Y') }}) Sales</h4>
                                    
                                </div>
                                <div class="card-content">
                                    <div class="card-body pb-0">
										<div class="row">
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Sales</p>
                                                <h2 class="text-bold-400">
                                                    <span class="text-success">${{ number_format($salestotal, 2) }}</span>
                                                </h2>
											</div>
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Paid</p>
                                                <h2 class="text-bold-400">
                                                    <span>${{ number_format($paid, 2) }}</span>
                                                </h2>
											</div>
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Pending</p>
                                                <h2 class="text-bold-400">                                                    
                                                    <span>${{ number_format($pending, 2) }}</span>
                                                </h2>
											</div>
										</div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
							
							<div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4 class="card-title">Last Month ({{ date('M Y', strtotime('last month')) }}) Sales</h4>
                                    
                                </div>
                                <div class="card-content">
                                    <div class="card-body pb-0">
										<div class="row">
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Sales</p>
                                                <h2 class="text-bold-400">
                                                    <span class="text-success">${{ number_format($lastsalestotal, 2) }}</span>
                                                </h2>
											</div>
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Paid</p>
                                                <h2 class="text-bold-400">
                                                    <span>${{ number_format($lastpaid, 2) }}</span>
                                                </h2>
											</div>
											<div class="col-md-4">
												<p class="mb-50 text-bold-600">Pending</p>
                                                <h2 class="text-bold-400">                                                    
                                                    <span>${{ number_format($lastpending, 2) }}</span>
                                                </h2>
											</div>
										</div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4 class="mb-0">Overall Overview</h4>
                                    <p class="font-medium-5 mb-0"><i class="feather icon-help-circle text-muted cursor-pointer"></i></p>
                                </div>
                                <div class="card-content">
                                    <div class="card-body px-0 pb-0">
                                        <div id="goal-overview-chart" class="mt-75"></div>
                                        <div class="row text-center mx-0">
                                            <div class="col-6 border-top border-right d-flex align-items-between flex-column py-1">
                                                <p class="mb-50">Paid ({{ $paidpercentage }}%)</p>
                                                <p class="font-large-1 text-bold-700">{{ number_format($overallpaid, 2) }}</p>
                                            </div>
                                            <div class="col-6 border-top d-flex align-items-between flex-column py-1">
                                                <p class="mb-50">Pending ({{ $pendingpercentage }}%)</p>
                                                <p class="font-large-1 text-bold-700">{{ number_format($overallpending, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-header d-flex justify-content-between align-items-end">
							<h4 class="mb-0">Recent Orders</h4>
						</div>
						<div class="card-content">
							<div class="card-body px-0 pb-0">
								<div class="row">
									<div class="col-md-12 col-12">
										<div class="table-responsive">
										<table class="table table-striped dataex-html5-selectors" id="subscriber">
										<thead>
											<tr style="background:#CCC;">                                    
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
										@if(count($recentorders) > 0)
											@foreach($recentorders as $order)
												<tr>
														
														<td>
														@php
															$orderid = $order->order_id;
															if(strlen($orderid) == 3) {
																$orderid = date('Ymd', strtotime($order->date_entered)).'0'.$orderid;
															} elseif(strlen($orderid) == 2) {
																$orderid = date('Ymd', strtotime($order->date_entered)).'00'.$orderid;
															} elseif(strlen($orderid) == 1) {
																$orderid = date('Ymd', strtotime($order->date_entered)).'000'.$orderid;
															} else {
																$orderid = date('Ymd', strtotime($order->date_entered)).$orderid;
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
														<a href="{{ url('/admin/orders/'.$order->order_id) }}"><i class="fa fa-search" aria-hidden="true"></i></a>
														</td>
												</tr>
											@endforeach
										@else
											<tr><td colspan="9" align="center">No Records Found</td></tr>
										@endif
										</tbody>
										</table>
									</div>                        
								</div>
							</div>
						</div>
					</div>		
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
@include('admin.includes.footer')

<script type="text/javascript">
$(window).on("load", function () {
	var $primary = '#7367F0';
	var $success = '#28C76F';
	var $danger = '#EA5455';
	var $warning = '#FF9F43';
	var $info = '#00cfe8';
	var $primary_light = '#A9A2F6';
	var $danger_light = '#f29292';
	var $success_light = '#55DD92';
	var $warning_light = '#ffc085';
	var $info_light = '#1fcadb';
	var $strok_color = '#b9c3cd';
	var $label_color = '#e7e7e7';
	var $white = '#fff';
	
	var goalChartoptions = {
	chart: {
	  height: 250,
	  type: 'radialBar',
	  sparkline: {
		enabled: true,
	  },
	  dropShadow: {
		enabled: true,
		blur: 3,
		left: 1,
		top: 1,
		opacity: 0.1
	  },
	},
	colors: [$success],
	plotOptions: {
	  radialBar: {
		size: 110,
		startAngle: -150,
		endAngle: 150,
		hollow: {
		  size: '77%',
		},
		track: {
		  background: $strok_color,
		  strokeWidth: '50%',
		},
		dataLabels: {
		  name: {
			show: false
		  },
		  value: {
			offsetY: 18,
			color: '#99a2ac',
			fontSize: '4rem'
		  }
		}
	  }
	},
	fill: {
	  type: 'gradient',
	  gradient: {
		shade: 'dark',
		type: 'horizontal',
		shadeIntensity: 0.5,
		gradientToColors: ['#00b5b5'],
		inverseColors: true,
		opacityFrom: 1,
		opacityTo: 1,
		stops: [0, 100]
	  },
	},
	series: [{{ $paidpercentage }}],
	stroke: {
	  lineCap: 'round'
	},

  }

  var goalChart = new ApexCharts(
	document.querySelector("#goal-overview-chart"),
	goalChartoptions
  );

  goalChart.render();
});	

</script>