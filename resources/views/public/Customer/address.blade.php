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
            <h3 class="mb-10">Addresses</h3>

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
            

            <!-- Form -->
            <form action="{{ url('/customer/updateprofile') }}" method="POST">
				@csrf
              <div class="row">
                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label for="firstName">First Name *</label>
                    <input class="form-control" id="firstName" name="cust_firstname" type="text" placeholder="First Name" value="{{ $customer->cust_firstname }}" required>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label for="lastName">Last Name *</label>
                    <input class="form-control" id="lastName" type="text" name="cust_lastname" placeholder="Last Name" value="{{ $customer->cust_lastname }}" required>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="emailAddress">Email Address *</label>
                    <input class="form-control" id="emailAddress" type="email" name="cust_email" placeholder="Email Address" value="{{ $customer->cust_email }}" disabled>
                  </div>
                </div>
				<div class="col-12">
                  <div class="form-group">
                    <label for="mobilePhone">Mobile Phone *</label>
					<div class="row">
						<div class="col-2">
							<select class="form-control" id="cust_countrycode" name="cust_countrycode">
							<option value="0">Dial Code</option>
							@if($countries)
								@foreach($countries as $country)
									<option value="{{ $country->phonecode }}" @if($customer->cust_countrycode == $country->phonecode) selected @endif>{{ $country->countrycode.' - '.$country->phonecode }}</option>
								@endforeach
							@endif	
							</select>
						</div>
						<div class="col-10">
							<input class="form-control" id="mobilePhone" type="tel" name="cust_phone" placeholder="Mobile Phone" value="{{ $customer->cust_phone }}" required>
						</div>
					</div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="companyName">Company Name</label>
                    <input class="form-control" id="companyName" type="text" name="cust_company" placeholder="Company Name" value="{{ $customer->cust_company }}" >
                  </div>
                </div>                
                <div class="col-12">
                  <div class="form-group">
                    <label for="addressLineOne">Address Line 1 *</label>
                    <input class="form-control" id="addressLineOne" type="text" name="cust_address1" placeholder="Address Line 1" value="{{ $customer->cust_address1 }}" required>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="addressLineTwo">Address Line 2</label>
                    <input class="form-control" id="addressLineTwo" type="text" name="cust_address2" value="{{ $customer->cust_address2 }}" placeholder="Address Line 2" >
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="townCity">Town / City *</label>
                    <input class="form-control" id="townCity" type="text" placeholder="Town / City" name="cust_city" value="{{ $customer->cust_city }}" required>
                  </div>
                </div>
				<div class="col-12">
                  <div class="form-group">
                    <label for="townCity">State *</label>
                    <input class="form-control" id="townCity" type="text" placeholder="State" name="cust_state" value="{{ $customer->cust_state }}" required>
                  </div>
                </div>
				<div class="col-12">
                  <div class="form-group">
                    <label for="country">Country *</label>					
					<select class="form-control" id="country" name="cust_country">
					@if($countries)
						@foreach($countries as $country)
							<option value="{{ $country->countryid }}" @if($customer->cust_country == $country->countryid) selected @endif>{{ $country->countryname }}</option>
						@endforeach
					@endif	
					</select>                    
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="zipPostcode">ZIP / Postcode *</label>
                    <input class="form-control" id="zipPostcode" type="text" placeholder="ZIP / Postcode" name="cust_zip" value="{{ $customer->cust_zip }}" required>
                  </div>
                </div>                
                
              </div>

              <!-- Button -->
              <button class="btn btn-dark" type="submit">
                Submit
              </button>

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