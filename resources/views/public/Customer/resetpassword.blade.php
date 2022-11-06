@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Reset Password</div></li>
        </ol>
      </div>
    </section>

	@includeIf('public.messages')
		
    <!-- Login Section Begin -->
	<section class="login">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="n-customer">
                            <h5>New User</h5>
                            <p>Register with us for future convenience:
							<ul class="marlef50"><li>Buy online</li>
							<li>Access your order history</li>
							<li>Be informed on new promotions and offers</li></ul></p>
                            <a href="#" class="redbtn">Create an Account</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="r-customer">
                            <h5>Reset Password</h5>
							
                            <form action="{{ url('/updateresetpassword') }}" method="POST" onsubmit="return validateform();">
								@csrf
								<input type="hidden" name="id" value="{{ $customerid }}">
							  <div class="row">
								
								<div class="col-6">
								  <div class="form-group">
									<label for="cust_lastname">
									  New Password *
									</label>
									<input class="form-control form-control-sm" name="new_password" id="new_password" type="password" placeholder="New Password" value="{{ old('new_password') }}" required pattern=".{6,50}" title="Password should be more than 6 characters">
								  </div>

								</div>
								<div class="col-6"></div>
								<div class="col-6">
								  <div class="form-group">
									<label for="cust_lastname">
									  Confirm Password *
									</label>
									<input class="form-control form-control-sm" name="confirm_password" id="confirm_password" type="password" placeholder="Confirm Password" value="{{ old('confirm_password') }}" required>
								  </div>

								</div>
								<div class="col-6"></div>
								<div class="col-12" id="passerror" style="display:none; color:red; padding-bottom:20px;">New Password and Confirm Password Mismatch!</div>
								<div class="col-12">

								  <!-- Button -->
								  <button class="redbtn" type="submit">Submit</button>

								</div>
							  </div>
							</form>
                        </div>
                    </div>
                </div>
            </div>
        </section>	

@include('footer')
    </body>
</html>
<script type="text/javascript">
function validateform() {
	var newpass = $('#new_password').val();
	var confirmpass = $('#confirm_password').val();
	if(newpass != confirmpass) {
		$('#passerror').show();
		return false;
	}
}
</script>
