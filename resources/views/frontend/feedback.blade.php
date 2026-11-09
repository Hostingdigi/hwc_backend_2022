@extends('layouts.app')
@section('content')
<div class="container">
	<div class="col-12 section-title mt-5 mb-3 text-center">
		<h2>Feedback</h2>
	</div>
    <div class="row justify-content-center">		
        <div class="col-md-12">
				@includeIf('frontend.messages')
			    <section class="register">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ url('submitfeedback') }}">
                        @csrf
						
						<input type="hidden" name="allowsubmit" id="allowsubmit" value="YES">
                            <h5>Leave Us Your Feedback</h5>
                            <div class="row">                                
                                <div class="col-md-12">
                                    <label>Name <span style="color: red;">*</span></label>
                                    <input type="text" name="name" id="name" required placeholder="Enter Your Name" maxlength="100">
                                </div>
								<div class="col-md-12">
                                    <label>Email <span style="color: red;">*</span></label>
                                    <input type="email" name="email" id="email" required placeholder="Enter Your Email" maxlength="255">
                                </div>
								<div class="col-md-12">
                                    <label>Phone <span style="color: red;">*</span></label>
                                    <input type="text" name="phone" id="phone" required placeholder="Enter Your Phone" maxlength="100">
                                </div>								
								<div class="col-md-12">
                                    <label>Message <span style="color: red;">*</span></label>
                                    <textarea id="message" name="message" required></textarea>
                                </div>
                                @env(['production','staging'])
								<!-- Google reCAPTCHA widget -->
                                <div class="col-md-12">
                                    <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_CAPTCHA_SITE_KEY') }}" data-badge="inline"  data-callback="setResponse"></div>
                                    <br>
                                    <input type="hidden" id="captcha-response" name="captcha_response" />
                                </div>						
                                @endenv		
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
@endsection
@push('child-scripts')
@env(['production','staging'])
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script>
<script>
var onloadCallback = function() {
    grecaptcha.execute();
};

function setResponse(response) { 
    document.getElementById('captcha-response').value = response; 
}
</script>
@endenv
@endpush
