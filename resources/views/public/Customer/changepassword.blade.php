@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">My Account</div></li>
        </ol>
      </div>
    </section>
	
 <!-- MyAccount -->
       <section class="pt-7 pb-12">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">

            <!-- Heading -->
            <h3 class="mb-10">Change Password</h3>

          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-3">

            <!-- Nav -->
            @includeIf('public/Customer.leftnav')


          </div>
			
          <div class="col-12 col-md-9 col-lg-8 offset-lg-1">
			@if(Session::get('success'))
			<div class="alert success">{{ Session::get('success') }}</div>
			@endif
			@if(Session::get('message'))
			<div class="alert dancer">{{ Session::get('message') }}</div>
			@endif
            <!-- Form -->
            <form action="{{ url('/customer/updatepassword') }}" method="POST" onsubmit="return validateform();">
				@csrf
              <div class="row">
                <div class="col-6">

                  <!-- Email -->
                  <div class="form-group">
                    <label for="old_password">
                      Old Password *
                    </label>
                    <input class="form-control form-control-sm" name="old_password" id="old_password" type="password" placeholder="Old Password" value="{{ old('old_password') }}" required>
                  </div>

                </div>
				<div class="col-6"></div>
                <div class="col-6">

                  
                  <div class="form-group">
                    <label for="cust_lastname">
                      New Password *
                    </label>
                    <input class="form-control form-control-sm" name="new_password" id="new_password" type="password" placeholder="New Password" value="{{ old('new_password') }}" required minlength="6">
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
    </section>
        <!-- End MyAccount -->




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