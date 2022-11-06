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
            <h3 class="mb-10">Personal Info</h3>

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
            <form action="{{ url('/customer/updatepersonalinfo') }}" method="POST">
				@csrf
              <div class="row">
                <div class="col-12 col-md-6">

                  <!-- Email -->
                  <div class="form-group">
                    <label for="cust_firstname">
                      First Name *
                    </label>
                    <input class="form-control form-control-sm" name="cust_firstname" id="cust_firstname" type="text" placeholder="First Name *" value="{{ $customer->cust_firstname }}" required>
                  </div>

                </div>
                <div class="col-12 col-md-6">

                  <!-- Email -->
                  <div class="form-group">
                    <label for="cust_lastname">
                      Last Name *
                    </label>
                    <input class="form-control form-control-sm" name="cust_lastname" id="cust_lastname" type="text" placeholder="Last Name *" value="{{ $customer->cust_lastname }}" required>
                  </div>

                </div>
                <div class="col-12">

                  <!-- Email -->
                  <div class="form-group">
                    <label for="accountEmail">
                      Email Address *
                    </label>
                    <input class="form-control form-control-sm" id="accountEmail" type="email" placeholder="Email Address *" value="{{ $customer->cust_email }}" disabled>
                  </div>

                </div>
                
				@php
				$dob = [];
				$dobdate = $dobmonth = $dobyear = '';
				if($customer->cust_dob != '') { 
					$dob = @explode('-', $customer->cust_dob);
					$dobdate = $dob[2];
					$dobmonth = $dob[1];
					$dobyear = $dob[0];
				}	
				@endphp
				
                <div class="col-12 col-lg-6">

                  <!-- Birthday -->
                  <div class="form-group">

                    <!-- Label -->
                    <label>Date of Birth</label>

                    <!-- Inputs -->
                    <div class="form-row">
                      <div class="col-auto">

                        <!-- Date -->
                        <label class="sr-only" for="accountDate">
                          Date
                        </label>
                        <select class="custom-select custom-select-sm" id="accountDate" name="dob_date">
							<option value=""></option>
							<?php
								for($d = 1; $d <= 31; $d++) {
								?><option value="{{ $d }}" @if($dobdate == $d) selected @endif>{{ $d }}</option><?php
								}
							?>                         
                        </select>

                      </div>
                      <div class="col">

                        <!-- Date -->
                        <label class="sr-only" for="accountMonth">
                          Month
                        </label>
                        <select class="custom-select custom-select-sm" id="accountMonth" name="dob_month">
							<option value=""></option>
							<option value="01" @if($dobmonth == '01') selected @endif>January</option>
							<option value="02" @if($dobmonth == '02') selected @endif>February</option>
							<option value="03" @if($dobmonth == '03') selected @endif>March</option>
							<option value="04" @if($dobmonth == '04') selected @endif>April</option>
							<option value="05" @if($dobmonth == '05') selected @endif>May</option>
							<option value="06" @if($dobmonth == '06') selected @endif>June</option>
							<option value="07" @if($dobmonth == '07') selected @endif>July</option>
							<option value="08" @if($dobmonth == '08') selected @endif>August</option>
							<option value="09" @if($dobmonth == '09') selected @endif>September</option>
							<option value="10" @if($dobmonth == '10') selected @endif>October</option>
							<option value="11" @if($dobmonth == '11') selected @endif>November</option>
							<option value="12" @if($dobmonth == '12') selected @endif>December</option>
                        </select>

                      </div>
                      <div class="col-auto">

                        <!-- Date -->
                        <label class="sr-only" for="accountYear">
                          Year
                        </label>
                        <select class="custom-select custom-select-sm" id="accountYear" name="dob_year">
							<option value=""></option>
							<?php
								for($y = date("Y"); $y >= 1950; $y--) {
								?><option value="{{ $y }}" @if($dobyear == $y) selected @endif>{{ $y }}</option><?php
								}
							?>
                        </select>

                      </div>
                    </div>

                  </div>

                </div>
                <div class="col-12 col-lg-6">

                  <!-- Gender -->
                  

                </div>
                <div class="col-12">

                  <!-- Button -->
                  <button class="btn btn-dark" type="submit">Save Changes</button>

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