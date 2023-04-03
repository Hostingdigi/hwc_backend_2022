@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->
    <div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:60%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Payment Methods</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/paymentmethods') }}" class="btn " tabindex="0" aria-controls="paymentmethods"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                        <form class="form form-horizontal" name="paymentmethods" id="paymentmethods" method = "post" action="{{ url('/admin/paymentmethods') }}" enctype="multipart/form-data">                                            
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Name <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="payment_name" class="form-control" name="payment_name" placeholder="Name" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Payment method</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select id="payment_mode" class="form-control" name="payment_mode">
																	<option value="test">Test mode</option>
																	<option value="live">Live mode</option>
																</select>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Test Mode URL</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="testing_url" class="form-control" name="testing_url" placeholder="Testing URL">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Test Mode - API Key</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="test_api_key" class="form-control" name="test_api_key" placeholder="API Key">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Test Mode - API Signature</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="test_api_signature" class="form-control" name="test_api_signature" placeholder="API Signature">
                                                            </div>
                                                        </div>
                                                    </div>
													<hr>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Live URL</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="live_url" class="form-control" name="live_url" placeholder="Live URL">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Live Mode - API Key</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="api_key" class="form-control" name="api_key" placeholder="API Key">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Live Mode - API Signature</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="api_signature" class="form-control" name="api_signature" placeholder="API Signature">
                                                            </div>
                                                        </div>
                                                    </div>
													<hr>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Email ID </span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="email" id="email" class="form-control" name="email" placeholder="Email" required>
                                                            </div>
                                                        </div>
                                                    </div>                                                     
												     <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                            <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
															<select name="status" id="status" class="form-control">
																<option value="1">Active</option>
																<option value="0">In-Active</option>
                                                            </select>    
                                                        </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Payment Logo </span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="file" id="payment_logo" class="form-control" name="payment_logo" placeholder="Payment Logo" required>
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
                    </div>
                </section>


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<!--script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script-->
