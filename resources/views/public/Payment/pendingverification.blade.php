@include('header', ['paymentPending' => true])
<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Payment Pending</div></li>
        </ol>
      </div>
    </section>
<!-- Payment pending verification -->
    <section class="py-12">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-8 col-xl-6 text-center">
           <br><br>
            <h2 class="mb-5">We are confirming your payment</h2>
            <p class="mb-7 text-gray-500">
              We could not confirm your payment with the payment provider yet.
              If you were charged, our team will confirm your order shortly.
              @if(!empty($orderid))
              Please quote order reference <strong>{{ $orderid }}</strong> if you contact us.
              @endif
            </p>
            <a class="btn btn-dark" href="{{ url('/') }}">
              Continue Shopping
            </a>
          </div>
        </div>
      </div>
    </section>
<!-- End Payment pending verification -->
@include('footer')
