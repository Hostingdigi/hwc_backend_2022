@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Login</div></li>
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
                            <a href="{{ url('register') }}" class="redbtn">Create an Account</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="r-customer">
                            <h5>Registered Customer</h5>
                            <p>If you have an account with us, please log in.</p>
							
                            <form action="{{ url('/logincheck') }}" name="login_frm" method="post">
								@csrf
                                <div class="emal">
                                    <label>User name or email address</label>
                                    <input type="text" name="username" id="username" value="{{ old('username') }}" required>
                                </div>
                                <div class="pass">
                                    <label>Password</label>
                                    <input type="password" name="password" id="password" required>
                                </div>
                                <div class="d-flex justify-content-between nam-btm">
                                    <div>
                                        <input type="checkbox" name="rememberme" id="rmme">
                                        <label for="rmme">Remember Me</label>
                                    </div>
                                    <div>
                                        <a href="{{ url('/forgotpassword') }}">Forgot password?</a>
                                    </div>
                                </div>
                                <button type="submit" name="button" class="redbtn">Log In</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>	
    <!-- Login Details Section End -->



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