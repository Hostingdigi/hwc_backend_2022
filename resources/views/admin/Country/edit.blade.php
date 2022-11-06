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
                            <h2 class="content-header-title float-left mb-0">Manage Shipping Country</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/country') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
<div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
						
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add New</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                       <form class="form form-horizontal" name="editcountry" id="editcountry" method = "post" action="{{ url('/admin/country/update' ) }}" enctype="multipart/form-data">
										<input type="hidden" name="id" value="{{ $country->countryid }}">
										<input type="hidden" name="_method" value="PUT">                                           
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Country Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="countryname" value="{{ $country->countryname }}" class="form-control" name="countryname" placeholder="Country Name" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Country Code</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="countrycode" value="{{ $country->countrycode }}" class="form-control" name="countrycode" placeholder="Country Code" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Tax Title</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="taxtitle" class="form-control" value="{{ $country->taxtitle }}" name="taxtitle" placeholder="Tax Title" >
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Tax Percentage (%)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="taxpercentage" class="form-control" value="{{ $country->taxpercentage }}" name="taxpercentage" placeholder="Tax Percentage" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <select name="country_status" class="form-control">
																	<option value="0" @if($country->country_status == 0) selected @endif>In-Active</option>
																	<option value="1" @if($country->country_status == 1) selected @endif>Active</option>
																</select>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Add</button>
                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
							
                        </div>
						</section>
                    </div>
                


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')