@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Customer</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group">
              <a href="{{ url('/admin/customer') }}" class="btn " tabindex="0" aria-controls="categories">
              <span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
						
                      <div class="col-md-12 col-12">
                            <div class="card">   
                                <div class="card-content">
                                    <div class="card-body">
                                    <form class="form form-horizontal" name="customer" id="customer" method = "post" action="{{ url('/admin/customer/'.$customer->cust_id) }}" enctype="multipart/form-data">                                            			<input type="hidden" name="id" value="{{ $customer->cust_id }}">			
                                    <input type="hidden" name="_method" value="PUT">
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                              <div class="row">
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>First Name <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_firstname" class="form-control" name="cust_firstname" placeholder="First Name" value="{{ $customer->cust_firstname }}" required>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Last Name <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_lastname" class="form-control" name="cust_lastname" placeholder="Last Name" value="{{ $customer->cust_lastname }}" required>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Email (Normal User only)</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="email" id="cust_email" class="form-control" name="cust_email" placeholder="Email"  value="{{ $customer->cust_email }}">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Username (Corporate User only)</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_username" class="form-control" name="cust_username" placeholder="Username" value="{{ $customer->cust_username }}">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <!--div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Password</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="password" id="cust_password" class="form-control" name="cust_password" placeholder="Password" required>
                                                          </div>
                                                      </div>                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Confirm Password</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="password" id="cust_cpassword" class="form-control" name="cust_cpassword" placeholder="Confirm Password" required>
                                                          </div>
                                                      </div>
                                                  </div-->
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Group</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                          <select name="cust_type" class="form-control" id = "cust_type">
                                                            @if($groups)
																@foreach($groups as $group)
                                                                  <option value="{{ $group->Id }}" @if($group->Id == $customer->cust_type) selected @endif>{{ $group->GroupTitle }}</option>
																@endforeach  
															@endif 
                                                          </select>                                                             
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Access Level</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                          <input type="checkbox" name="group_admin" value="1" @if($customer->group_admin == 1) checked @endif > Group Admin
                                                          </div>
                                                      </div>
                                                  </div>                         
                                                  <h1>Contact Particulars</h1>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Address Line 1 <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                         <textarea class="form-control" name= "cust_address1" id = "cust_address1" value="" required>{{ $customer->cust_address1 }}</textarea>
                                                          </div>
                                                      </div>
                                                  </div>  
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Address Line 2</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                         <textarea class="form-control" name= "cust_address2" id = "cust_address2">{{ $customer->cust_address2 }}</textarea>
                                                          </div>
                                                      </div>
                                                  </div>  
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>City <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_city" class="form-control" name="cust_city" placeholder="City" value="{{ $customer->cust_city }}" required>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>State</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_state" class="form-control" name="cust_state" placeholder="State" value="{{ $customer->cust_state }}">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Country <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                          <select name="cust_country" class='form-control' id = "cust_country" required>
                                                          @if($countries)						
                                                            @foreach($countries as $country)							
                                                                <option value="{{ $country->countryid }}" @if($customer->cust_country == $country->countryid) selected @endif>{{ $country->countryname }}</option>						
                                                            @endforeach					
                                                            @endif
                                                            </select> 
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Zip Code <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_zip" class="form-control" name="cust_zip" placeholder="Zip Code" value="{{ $customer->cust_zip }}" required>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Mobile <span class="badge badge-dot badge-danger"> </span></span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_phone" class="form-control" name="cust_phone" placeholder="Mobile" value="{{ $customer->cust_phone }}" required>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Landline</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_landline" class="form-control" name="cust_landline" placeholder="Landline" value="{{ $customer->cust_landline }}">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Office</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="text" id="cust_office" class="form-control" name="cust_office" placeholder="Office" value="{{ $customer->cust_office }}">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>I wish to receive newsletter and promotion events</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <input type="checkbox"  name="cust_newsletter" value="1" @if($customer->cust_newsletter == 1) checked @endif>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-12">
                                                      <div class="form-group row">
                                                          <div class="col-md-4">
                                                              <span>Status</span>
                                                          </div>
                                                          <div class="col-md-8">
                                                          <select name="cust_status" class="form-control">
                                                                <option value="0" @if($customer->cust_status == 0) selected @endif >In-Active</option>
                                                                <option value="1" @if($customer->cust_status == 1) selected @endif>Active</option>
                                                           </select>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Save</button>
                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
							
                        </div>
                    </div>
                </section>


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')
    
    
	