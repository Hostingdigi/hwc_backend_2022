@include('header')


<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="{{ url('/') }}">Home</a></li>
		  <li><div class="pageheading">Fogot Password</div></li>
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
                            <a href="{{ url('/register') }}" class="redbtn">Create an Account</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="r-customer">
                            <h5>Forgot Password</h5>							
                            <form action="{{ url('/sendforgotemail') }}" method="POST" >
								@csrf
                                <div class="emal">
                                    <label>User name or email address</label>
                                    <input type="text" name="cust_email" id="cust_email" required value="{{ old('cust_email') }}">
                                </div>
                                                                
                                <button type="submit" name="button" class="redbtn">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>	

@include('footer')
    </body>
</html>
