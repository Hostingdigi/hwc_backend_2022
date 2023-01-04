@include('header')
<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="index.html">Home</a></li>
		  <li><div class="pageheading">Checkout</div></li>
        </ol>
      </div>
    </section>
	
<!-- Checkout -->
<section class="checkout">
	<form role="form" action="{{ url('stripePaymentProcess') }}" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="{{ $stripekey }}" id="payment-form">
     @csrf
	 <input type="hidden" name="orderincid" class="orderincid" value="{{ $orderincid }}">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<div class="pay-meth">
						<div class='row'>
                            <div class='col-xs-12'>
                                <label class='control-label'><img src="{{ url('/images/visa.jpg') }}"></label>
								</div>
                        </div> 
                        <div class='row'>
                            <div class='col-xs-12'>
                                <label class='control-label'>Card Number</label> 
								<input autocomplete='off' class='form-control card-number' size='100' type='text' maxlength="20" required name="cardnumber">
								</div>
                        </div>  
                        <div class='row'>
                            <div class='col-xs-12 col-md-4'>
                                <label class='control-label'>CVC</label> 
								<input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text' maxlength="4" required name="cvc">
                            </div>
                            <div class='col-xs-12 col-md-4'>
                                <label class='control-label'>Expiration Month</label> 
								<select name="exp_month" data-stripe="exp-month" class="card-expiry-month stripe-sensitive required form-control" required style="height: calc(2.25rem + 10px);">
									<option value="01" selected="selected">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
								 </select>
                            </div>
                            <div class='col-xs-12 col-md-4'>
                                <label class='control-label'>Expiration Year</label> 
								<select name="exp_year" data-stripe="exp-year" class="card-expiry-year stripe-sensitive required form-control" required style="height: calc(2.25rem + 10px);">
								</select>
                            </div>
                        </div>
						
						<input type="hidden" name="cust_name" class="card-holder-name" value="{{ $billinginfo['bill_fname'].' '.$billinginfo['bill_lname'] }}">
						<input type="hidden" name="address" class="address" value="{{ $billinginfo['bill_ads1'] }}">
						<input type="hidden" name="city" class="city" value="{{ $billinginfo['bill_city'] }}">
						<input type="hidden" name="state" class="state" value="{{ $billinginfo['bill_state'] }}">
						<input type="hidden" name="country" class="country" value="{{ $billinginfo['bill_country'] }}">
						<input type="hidden" name="zip" class="zip" value="{{ $billinginfo['bill_zip'] }}">
                        
                          
                   
				</div>				
				<div class="row">
					<div class="col-xs-12 col-md-12">
						<button type="submit" name="button" class="ord-btn" id="ordbtn">Place Order</button>
						<button type="button" name="button" class="ord-btn" id="loading" style="display:none;">Loading...Please Wait</button>
					</div>
				</div>
				
			</div>
			
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<div class="order-review">
							<h5>Order Summary</h5>
							<div class="review-box">
								<ul class="list-unstyled">
									<li>Product <span>Total</span></li>
									@if($cartdata)
										@foreach($cartdata as $cartkey => $cart)
										<li class="d-flex justify-content-between">
											<div class="pro">
												<div class="row">
													<div class="col-md-3">
														@if($cart['image'] != '')															
															<img src="{{ url('/uploads/product/'.$cart['image']) }}" alt="{{ $cart['productName'] }}" width="100%">
														@else
															<img src="{{ url('/images/noimage.png') }}" alt="{{ $cart['productName'] }}" width="100%">
														@endif
													</div>
													<div class="col-md-9 no-pm">
														<p>{{ $cart['productName'] }}</p>
														<p class="hint">{{ $cart['qty'] }} X S${{ number_format($cart['price'],2) }}</p>
														@if($cart['productoption'])
															<p class="hint">Option: {{ $cart['productoption'] }}</p>
														@endif
														@if($cart['size'] && 1==2)
															<p class="hint">Size: {{ $cart['size'] }}</p>
														@endif
														@if($cart['productWeight'] && 1==2)
															<p class="hint">Weight: {{ $cart['productWeight'] }} Kg</p>
														@endif	
														@if($cart['color'] && 1==2)
															<p class="hint">Color: {{ $cart['color'] }}</p>
														@endif	
														
													</div>
												</div>
											</div>
											<div class="prc">
												<p>S${{ number_format($cart['total'],2) }}</p>
											</div>
										</li>
										@endforeach
									@endif
									
								<li class="subtot">Sub Total
									<span>S${{ number_format(str_replace(',','',$subtotal), 2) }}</span>
										@if(!empty($taxLabelOnly))
                						<small style="font-size:92%;word-break: break-word;"><br>[w/o - {{$taxLabelOnly}}]</small>
                						@endif
									</li>
									<li class="subtot" style="border-top: 1px solid #e5e5e5;">{{ $taxtitle }} <span>S${{ number_format(str_replace(',','',$gst), 2) }}</span></li>
									<li>Shipping ({{ $deliverytype }}) <span>S${{ number_format(str_replace(',','',$deliverycost), 2) }}</span></li>
									<li>Packaging Fee <span>S${{ number_format(str_replace(',','',$packingfee), 2) }}</span></li>
									@if($discount != 0 && $discounttext != '')<li id="dis">Discount({{ $discounttext }})<span>S${{ number_format(str_replace(',','',$discount), 2) }}</span></li>@endif
									<li>Grand Total <span>S${{ number_format(str_replace(',','',$grandtotal), 2) }}</span></li>
								</ul>
							</div>
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
	</form>
</section>
<!-- End Checkout -->

@include('footer')

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
  
<script type="text/javascript">
$(function() {
	
	$('#ordbtn').click(function() {
		$('#ordbtn').hide();
		$('#loading').show();
	});
	
	$('.card-number').keypress(function (e) {
     
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        
               return false;
		}
   });
   $('.card-cvc').keypress(function (e) {
     
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        
               return false;
		}
   });
   
	var select = $(".card-expiry-year"),
	year = new Date().getFullYear();
	for (var i = 0; i < 12; i++) {
		select.append($("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
	}
    var $form         = $(".require-validation");
	$('form.require-validation').bind('submit', function(e) {
		var $form         = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('hide');
 
        $('.has-error').removeClass('has-error');
    $inputs.each(function(i, el) {
      var $input = $(el);
      if ($input.val() === '') {
        $input.parent().addClass('has-error');
        $errorMessage.removeClass('hide');
        e.preventDefault();
      }
    });
  
    if (!$form.data('cc-on-file')) {
      e.preventDefault();
      Stripe.setPublishableKey($form.data('stripe-publishable-key'));
      Stripe.createToken({
        number: $('.card-number').val(),
        cvc: $('.card-cvc').val(),
        exp_month: $('.card-expiry-month').val(),
        exp_year: $('.card-expiry-year').val(),
		name: $('.card-holder-name').val(),
		address_line1: $('.address').val(),
		address_city: $('.city').val(),
		address_zip: $('.zip').val(),
		address_state: $('.state').val(),
		address_country: $('.country').val()		
      }, stripeResponseHandler);
    }
  
  });
  
  function stripeResponseHandler(status, response) {
		console.log(response);
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            // token contains id, last4, and card type
            var token = response['id'];
            // insert the token into the form so it gets submitted to the server
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
  
});
</script>

