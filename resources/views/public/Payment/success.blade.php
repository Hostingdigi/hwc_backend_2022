@include('header')

<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Order Complete</div></li>
        </ol>
      </div>
    </section>
<!-- Order Completed -->
    <section class="py-12">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-8 col-xl-6 text-center">

            <!-- Icon -->
           <br><br>
            <!-- Heading -->
            <h2 class="mb-5">Your Order is Completed!</h2>

            <!-- Text -->
            <p class="mb-7 text-gray-500">
              Your order details are shown for your personal account.
              @if(isset($ordermastertbl->order_id))
              <input type="hidden" name="Amount" value={{$ordermastertbl->payable_amount}}>
              <input type="hidden" name="Order_Id" value={{$ordermastertbl->order_id}}>
              @endif
            </p>

            <!-- Button -->
            <a class="btn btn-dark" href="{{ url('/customer/myorders') }}">
              View My Orders
            </a>

          </div>
        </div>
      </div>
    </section>
        <!-- Order Completed -->
@include('footer')