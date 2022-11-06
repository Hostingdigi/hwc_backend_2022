@include('header')



<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Register</div></li>
        </ol>
      </div>
    </section>
	

    <!-- Register -->
        <section class="register">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ url('customer') }}" name="registerform" id="registerform">
                        @csrf
						<input type="hidden" name="cust_dob" value="0000-00-00">
						<input type="hidden" name="allowsubmit" id="allowsubmit" value="YES">
                            <h5>PERSONAL INFORMATION</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>First Name*</label>
                                    <input type="text" name="cust_firstname" id="cust_firstname" required placeholder="Your first name" maxlength="100">
                                </div>
                                <div class="col-md-12">
                                    <label>Last Name*</label>
                                    <input type="text" name="cust_lastname" id="cust_lastname" required placeholder="Your Last name" maxlength="100">
                                </div>
								<div class="col-md-12">
                                    <label>Company Name</label>
                                    <input type="text" name="cust_company" id="cust_company" placeholder="Your Company name">
                                </div>
								<div class="col-md-12">
                                    <label>Phone*</label>
									<div class="row">
										<div class="col-md-3">
											<select name="cust_countrycode" ng-model="cust_countrycode" required="">
												@if($countries)
													@foreach($countries as $country)
														@if($country->phonecode > 0)
															<option value="{{ $country->phonecode }}" @if($country->countrycode == 'SG') selected @endif>{{ $country->countrycode.' - '.$country->phonecode }}</option>
														@endif
													@endforeach
												@endif	
											</select>											
										</div>
										<div class="col-md-9">
											<input type="number" name="cust_phone" id="cust_phone" required placeholder="Your Phone number" maxlength="50" >
										</div>
									</div>
                                </div>
								<div class="col-md-12">
                                    <label>Fax</label>
                                    <input type="number" name="cust_fax" id="cust_fax" placeholder="Your Fax number">
                                </div>
								<div class="col-md-12">
                                    <label>Address Line 1*</label>                                    
									<input type="text" name="cust_address1" id="cust_address1" placeholder="Address Line 1" required>
                                </div>
								<div class="col-md-12">
                                    <label>Address Line 2</label>                                    
									<input type="text" name="cust_address2" id="cust_address2" placeholder="Address Line 2">
                                </div>
								<div class="col-md-12">
                                    <label>Postal Code*</label>
                                    <input type="text" name="cust_zip" id="cust_zip" placeholder="Enter Postal code" maxlength="10" required>
                                </div>
								
								<div class="col-md-12">
								<label>HOW YOU GET TO KNOW US*</label>
									<select name="howyouknow" ng-model="howyouknow" required="">
										<option value="">Select</option>
										<option value="Google">Google</option>
										<option value="Yahoo">Yahoo</option>
										<option value="YellowPages">YellowPages</option>
										<option value="Friends referral">Friends referral</option>
										<option value="Bing">Bing</option>
										<option value="Others">Others</option>
									</select>
								</div>								
								<div class="col-md-12">
									<hr />
								</div>
								<div class="col-md-12">
									<h5>LOGIN INFORMATION</h5>
								</div>
								<span id="emailerror" class="error" style="display:none;">Email Address already Exist!</span>
                                <div class="col-md-12">
                                    <label>Email Address*</label>
                                    <input type="email" name="cust_email" id="cust_email" required placeholder="Your email address">
                                </div>
								
                                <div class="col-md-12">
                                    <label>Password*</label>
                                    <input type="password" name="cust_password" id="cust_password" required placeholder="Password should be more than 6 character" pattern=".{6,50}" title="Password should be more than 6 characters">
                                </div>
                                <div class="col-md-12">
                                    <label>Confirm Password*</label>
                                    <input type="password" name="cust_cpassword" id="cust_cpassword" required placeholder="Confirm your password">
                                </div>
								<span id="passerror" class="error" style="display:none;">Password and Confirm Password mismatch!</span>
                                <div class="col-md-7">
                                    <div>
                                        <input type="checkbox" name="cust_terms_agreed" id="cust_terms_agreed" required value="1">
                                        <label for="cust_terms_agreed">I have read the <a href="{{ url('/terms-and-conditions') }}">terms and condition</a>.</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" name="cust_newsletter" id="cust_newsletter" value="1">
                                        <label for="cust_newsletter">Subscribe for newsletter</label>
                                    </div>
                                </div>
                                <div class="col-md-5 text-right">
                                    <button type="submit" name="button">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Register -->




@include('footer')
    </body>
</html>

<script type="text/javascript">
$(document).ready(function() {
	$('#cust_firstname').keydown(function (e) {
        if (e.ctrlKey || e.altKey) {
             e.preventDefault();
        } else {
            var key = e.keyCode;
			
            if(!((key==8)||(key==9)||(key==32)||(key==46)||(key>=65 && key<=90)||(key==190))) {
                e.preventDefault();
            }
        }
    });
	$('#cust_lastname').keydown(function (e) {
        if (e.ctrlKey || e.altKey) {
             e.preventDefault();
        } else {
            var key = e.keyCode;
			
            if(!((key==8)||(key==9)||(key==32)||(key==46)||(key>=65 && key<=90)||(key==190))) {
                e.preventDefault();
            }
        }
    });
	$('#cust_email').blur(function() {
		var email = $('#cust_email').val();
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/chkcustomerexist',				
			data: {'id': 0, 'email': email},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {			
				if(response == 1) {				
					$('#emailerror').show();
					$('#allowsubmit').val('NO');
				} else {
					$('#emailerror').hide();
					$('#allowsubmit').val('YES');
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});
	});
	$('#cust_password').blur(function() {
		var pass = $('#cust_password').val();
		var cpass = $('#cust_cpassword').val();
		if(pass != '' && cpass != '') {
			if(pass != cpass) {
				$('#passerror').show();
				$('#allowsubmit').val('NO');
			} else {
				$('#passerror').hide();
				$('#allowsubmit').val('YES');
			}
		}
	});
	$('#cust_cpassword').blur(function() {
		var pass = $('#cust_password').val();
		var cpass = $('#cust_cpassword').val();
		if(pass != '' && cpass != '') {
			if(pass != cpass) {
				$('#passerror').show();
				$('#allowsubmit').val('NO');
			} else {
				$('#passerror').hide();
				$('#allowsubmit').val('YES');
			}
		}
	});
	$('#registerform').on('submit', function(e) {
		e.preventDefault();
		var pass = $('#cust_password').val();
		var cpass = $('#cust_cpassword').val();		
		if(pass != '' && cpass != '') {
			if(pass != cpass) {
				return false;
			}
		}
		var email = $('#cust_email').val();
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/chkcustomerexist',				
			data: {'id': 0, 'email': email},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {			
				if(response == 1) {				
					return false;
				} else {
					$('#registerform').unbind('submit').submit(); 
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});		
	});	
});


</script>
