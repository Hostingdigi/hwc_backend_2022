@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Orders</div></li>
        </ol>
      </div>
    </section>
	
 <!-- MyAccount -->
<section class="pt-7 pb-12">
    <div class="container">
        <div class="row">
          <div class="col-12 text-center">

            <!-- Heading -->
            <h3 class="mb-10">Track Order</h3>

          </div>
        </div>
        <div class="row">
			<div class="col-12 col-md-3">
            <!-- Nav -->
            @includeIf('public/Customer.leftnav')
			</div>
			
			<div class="col-12 col-md-9 col-lg-8 offset-lg-1">

            <!-- Order -->
            <div class="card card-lg mb-5 border">
              <div class="card-body pb-0">

                <!-- Info -->
                <div class="card card-sm">
                  <div class="card-body bg-light">
                    <div class="row">
                      <div class="col-12 col-lg-12">
						@php
							$orderincid = $orderid;
						@endphp
						
						@if(strlen($orderid) == 3)
							@php $orderid = '0'.$orderid; @endphp
						@elseif(strlen($orderid) == 2)
							@php $orderid = '00'.$orderid; @endphp
						@elseif(strlen($orderid) == 1)
							@php $orderid = '000'.$orderid; @endphp	
						@endif

						@php
							$orderid = date('Ymd', strtotime($orderdate)).$orderid;
						@endphp
                        <!-- Heading -->
                        <h6 class="heading-xxxs text-muted text-center">Order No: {{ $orderid }}</h6>

                      </div>
                      
                    </div>
					
                  </div>
                </div>
				<div class="row">
					<div class="col-6 col-lg-6">
						@includeIf('public.messages')
						<div class="r-customer">						
						<form action="{{ url('/trackorder/'.$orderincid) }}" method="POST" >
							@csrf
							<div class="emal">
								<label>Order Tracking Number</label>
								<input type="text" name="tracking_number" id="tracking_number" class="form-control" required value="{{ old('tracking_number') }}">
							</div>                          
							<button type="submit" name="button" class="site-btn bg-dark mx-auto textyellow" style="margin-top:20px;">Submit</button>
						</form>
					</div>
					</div>
				</div>
              </div>
              
    </div>
</section>
<!-- End MyAccount -->

@include('footer')
