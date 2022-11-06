@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">My Account</div></li>
        </ol>
      </div>
    </section>
	
 <!-- MyAccount -->
       <section class="pt-7 pb-12">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">

            <!-- Heading -->
            <h3 class="mb-10">Orders</h3>

          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-3">

            <!-- Nav -->
            @includeIf('public/Customer.leftnav')


          </div>
			
          <div class="col-12 col-md-9">
			@if($orders && count($orders) > 0)				
				@foreach($orders as $order)
					@php
						$orderdetails = [];
						$deliveryinfo = 0;
						$orderdetails = \App\Models\OrderDetails::where('order_id', '=', $order->order_id)->get();
						$deliveryinfo = \App\Models\OrderDeliveryInfo::where('order_id', '=', $order->order_id)->count();
					@endphp
					<div class="card card-lg mb-5 border">
					  <div class="card-body pb-0">

						<!-- Info -->
						<div class="card card-sm">
						  <div class="card-body bg-light">
							<div class="row">
							  <div class="col-6 col-lg-4">

								<!-- Heading -->
								<h6 class="heading-xxxs text-muted">Order No:</h6>

								<!-- Text -->
								<p class="mb-lg-0 font-size-sm font-weight-bold">
								@php 
									$orderid = $order->order_id;
								@endphp
								@if(strlen($order->order_id) == 3)
									@php $orderid = '0'.$order->order_id; @endphp
								@elseif(strlen($order->order_id) == 2)
									@php $orderid = '00'.$order->order_id; @endphp
								@elseif(strlen($order->order_id) == 1)
									@php $orderid = '000'.$order->order_id; @endphp	
								@endif

								@php
									$orderid = date('Ymd', strtotime($order->date_entered)).$orderid;
								@endphp
								
								{{ $orderid }}
								</p>

							  </div>
							  
							  <div class="col-6 col-lg-4">

								<!-- Heading -->
								<h6 class="heading-xxxs text-muted">Status:</h6>

								<!-- Text -->
								<p class="mb-0 font-size-sm font-weight-bold">
									@if($order->order_status == 0) Payment Pending @elseif($order->order_status == 1) Paid, Shipping Pending @elseif($order->order_status == 2) Shipped @elseif($order->order_status == 3) Shipped @elseif($order->order_status == 5) On the Way To You @elseif($order->order_status == 6) Partially Delivered @elseif($order->order_status == 7) Partially Refund @elseif($order->order_status == 8) Fully Refund @elseif($order->order_status == 9) Ready For Collection @endif
								</p>

							  </div>
							  <div class="col-6 col-lg-4">

								<!-- Heading -->
								<h6 class="heading-xxxs text-muted">Order Amount:</h6>

								<!-- Text -->
								<p class="mb-0 font-size-sm font-weight-bold">
								  ${{ number_format($order->payable_amount, 2) }}
								</p>

							  </div>
							</div>
						  </div>
						</div>

					  </div>
					  <div class="card-footer">
						<div class="row align-items-center">
						  <div class="col-12 col-lg-6">
							<div class="form-row mb-4 mb-lg-0">
								
								@if($orderdetails)
									@php 
										$ordercount = 1; 
										$productimage = '';
									@endphp
									@foreach($orderdetails as $orderdetail)
										@if($ordercount <= 3)
											@php
												$product = \App\Models\Product::where('Id', '=', $orderdetail->prod_id)->select('Image')->first();
												if($product) {
													$productimage = $product->Image;
												}
											@endphp
											<div class="col-3">
												@if($productimage)
													@php 
														$productimage = str_replace(' ','%20', $productimage);
													@endphp
													<div class="embed-responsive embed-responsive-1by1 bg-cover orderimg" style="background-image: url({{ url('/uploads/product/'.$productimage) }});"></div>
												@else
													<div class="embed-responsive embed-responsive-1by1 bg-cover orderimg" style="background-image: url({{ url('/images/noimage.png') }});"></div>
												@endif	
											</div>
										@endif
										@php ++$ordercount; @endphp
									@endforeach
									@if(count($orderdetails) > 3)
										@php 
											$productcount = count($orderdetails) - 3;										
										@endphp
										<div class="col-3">								
											<div class="embed-responsive embed-responsive-1by1 bg-light">
											  <a class="embed-responsive-item embed-responsive-item-text text-reset" href="{{ url('/orderdetails/'.$order->order_id) }}">
												<div class="font-size-xxs font-weight-bold">
												  +{{ $productcount }} <br> more
												</div>
											  </a>
											</div>
										</div>
									@endif
								@endif						  
								
							</div>
						  </div>
						  <div class="col-12 col-lg-6">
							<div class="form-row">
								@if($deliveryinfo == 0)
									<div  class="col-6"></div>
								@endif	
							  <div  class="col-6">

								<!-- Button -->
								<a class="btn btn-lg btn-block btn-outline-dark" href="{{ url('/orderdetails/'.$order->order_id) }}">
								  Order Details
								</a>

							  </div>
							  @if($deliveryinfo > 0)
							  <div class="col-6">

								<!-- Button -->
								<a class="btn btn-lg btn-block btn-outline-dark" href="{{ url('/orderdeliveryinfo/'.$order->order_id) }}">
								  Track order
								</a>

							  </div>
							  @endif
							</div>

						  </div>
						</div>
					  </div>
					</div>
				@endforeach
			@else
				<div class="row"><div class="col-md-12">No Orders Found!</div></div>
			@endif
            
			<div class="pagination-box" style="float:right;">
				@if ($orders->hasPages())
					<ul class="list-unstyled list-inline text-center">
						{{-- Previous Page Link --}}
						@if ($orders->onFirstPage())
							<li class="disabled list-inline-item"><span><<</span></li>
						@else
							<li class="list-inline-item"><a href="{{ $orders->appends($_GET)->previousPageUrl() }}" rel="prev"><<</a></li>
						@endif

						@if($orders->currentPage() > 3)
							<li class="hidden-xs list-inline-item"><a href="{{ $orders->appends($_GET)->url(1) }}">1</a></li>
						@endif
						@if($orders->currentPage() > 4)
							<li class="list-inline-item"><span>...</span></li>
						@endif
						@foreach(range(1, $orders->lastPage()) as $i)
							@if($i >= $orders->currentPage() - 2 && $i <= $orders->currentPage() + 2)
								@if ($i == $orders->currentPage())
									<li class="active list-inline-item"><a href="">{{ $i }}</a></li>
								@else
									<li class="list-inline-item"><a href="{{ $orders->appends($_GET)->url($i) }}">{{ $i }}</a></li>
								@endif
							@endif
						@endforeach
						@if($orders->currentPage() < $orders->lastPage() - 3)
							<li class="list-inline-item"><span>...</span></li>
						@endif
						@if($orders->currentPage() < $orders->lastPage() - 2)
							<li class="hidden-xs list-inline-item"><a href="{{ $orders->appends($_GET)->url($orders->lastPage()) }}">{{ $orders->lastPage() }}</a></li>
						@endif

						{{-- Next Page Link --}}
						@if ($orders->hasMorePages())
							<li class="list-inline-item"><a href="{{ $orders->appends($_GET)->nextPageUrl() }}" rel="next">>></a></li>
						@else
							<li class="disabled  list-inline-item"><span>>></span></li>
						@endif
					</ul>
				@endif
			</div>
			
          </div>
        </div>
      </div>
    </section>
        <!-- End MyAccount -->




@include('footer')
    </body>
</html>
<script type="text/javascript">
$(document).ready(function() {
	var email = $('#cust_email').val();
	var token = "{!! csrf_token() !!}";
	$.ajax({
		type: "POST",
		url: '{{ url("/") }}/chkcustomerexist',				
		data: {'id': 0, 'email': email},
		headers: {'X-CSRF-TOKEN': token},		
		success: function(response) {			
			if(response == 1) {				
				$('#emailerror').toggle();
				$('#allowsubmit').val('NO');
			} else {
				$('#allowsubmit').val('YES');
			}
		},error: function(ts) {				
			console.log("Error:"+ts.responseText);  
		}
	});
});

function checkformsubmit() {
	var allowsubmit = $('#allowsubmit').val();
	if(allowsubmit == 'NO') {
		return false;
	}
}
</script>