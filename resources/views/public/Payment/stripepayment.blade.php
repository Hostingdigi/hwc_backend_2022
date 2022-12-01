
	
<!-- Checkout -->
<section class="checkout">
	<form role="form" action="{{ url('stripeApiPaymentProcess') }}" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="{{ $apikey }}" id="payment-form">
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
								<input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text' maxlength="3" required name="cvc">
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
						
						<input type="hidden" name="cust_name" class="card-holder-name" value="{{ $order->bill_fname.' '.$order->bill_lname }}">
						<input type="hidden" name="address" class="address" value="{{ $order->bill_ads1 }}">
						<input type="hidden" name="city" class="city" value="{{ $order->bill_city }}">
						<input type="hidden" name="state" class="state" value="{{ $order->bill_state }}">
						<input type="hidden" name="country" class="country" value="{{ $order->bill_country }}">
						<input type="hidden" name="zip" class="zip" value="{{ $order->bill_zip }}">
                        
                          
                   
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
									
									<li class="subtot">Sub Total <span>S${{ $order->payable_amount - ($order->tax_collected + $order->shipping_cost + $order->packaging_fee)  }}</span></li>
									<li class="subtot" style="border-top: 1px solid #e5e5e5;">Tax <span>S${{ $order->tax_collected }}</span></li>
									<li>Shipping <span>S${{ $order->shipping_cost }}</span></li>
									<li>Packaging Fee <span>S${{ $order->packaging_fee }}</span></li>
									<li>Grand Total <span>S${{ $order->payable_amount }}</span></li>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

