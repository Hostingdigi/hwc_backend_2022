@include('header')
<div class="container">
	<div class="col-12 section-title mt-5 mb-3 text-center">
		<h2>Contact Us</h2>
	</div>
    <div class="row justify-content-center">		
        <div class="col-md-12">
				@includeIf('public.messages')
			    <section class="register">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ url('contactus') }}">
                        @csrf
						
						<input type="hidden" name="allowsubmit" id="allowsubmit" value="YES">
                            <h5>Leave Us a Message</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Your Salutation</label>
                                    <select name="salutation" id="salutation">
										<option value="Mr">Mr</option>
										<option value="Miss">Miss</option>
										<option value="Mrs">Mrs</option>
										<option value="Ms">Ms</option>
										<option value="Dr">Dr</option>
									</select>
                                </div>
                                <div class="col-md-12">
                                    <label>Name</label>
                                    <input type="text" name="name" id="name" required placeholder="Enter Your Name" maxlength="100">
                                </div>
								<div class="col-md-12">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" required placeholder="Enter Your Email" maxlength="255">
                                </div>
								<div class="col-md-12">
                                    <label>Phone</label>
                                    <input type="text" name="phone" id="phone" required placeholder="Enter Your Phone" maxlength="100">
                                </div>
								<div class="col-md-12">
                                    <label>Type of Enquiry</label>
                                    <select name="enquiry_type" id="enquiry_type" required>
										<option value="">Select</option>
										<option value="General Enquiry">General Enquiry</option>
										<option value="Members' Program">Members' Program</option>
										<option value="Compliments">Compliments</option>
										<option value="Product Information">Product Information</option>
										<option value="Order Information">Order Information</option>
										<option value="Program/Promotion Feedback">Program/Promotion Feedback</option>
										<option value="General Feedback">General Feedback</option>
									</select>
                                </div>
								<div class="col-md-12">
                                    <label>Message</label>
                                    <textarea id="message" name="message" required></textarea>
                                </div>
                                <!-- Google reCAPTCHA widget -->
                                <div class="col-md-12">
                                    <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_CAPTCHA_SITE_KEY') }}" data-badge="inline"  data-callback="setResponse"></div>
                                    <br>
                                    <input type="hidden" id="captcha-response" name="captcha_response" />
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" name="button">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Register -->
        </div>
    </div>
</div>
@push('child-scripts')
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script>
<script>
var onloadCallback = function() {
    grecaptcha.execute();
};

function setResponse(response) { 
    document.getElementById('captcha-response').value = response; 
}
</script>
@endpush
@include('footer')
