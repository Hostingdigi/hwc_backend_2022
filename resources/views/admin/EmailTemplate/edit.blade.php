@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
}
</style>
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->
    <div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Email Template</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/emailtemplate/') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                        <form class="form form-horizontal" name="addproducttag" id="addproducttag" method = "post" action="{{ url('/admin/emailtemplate/'.$emailtemplate->id) }}" enctype="multipart/form-data">                                            
                                        <input type="hidden" name="id" value="{{ $emailtemplate->id }}">			
                                    <input type="hidden" name="_method" value="PUT">
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
													<div class="col-12">
														<div class="form-group row">
															<div class="col-md-4">
																<span>Template Type <span class="badge badge-dot badge-danger"></span></span>
															</div>
															<div class="col-md-8">
																<select name="template_type" id="template_type" required class="form-control">
																	<option value="">Select Template Type</option>
																	<option value="1" @if($emailtemplate->template_type == 1) selected @endif>Customer Registration</option>
																	<option value="2" @if($emailtemplate->template_type == 2) selected @endif>New Order Confirmation</option>
																	<option value="3" @if($emailtemplate->template_type == 3) selected @endif>Invoice</option>
																	<option value="4" @if($emailtemplate->template_type == 4) selected @endif>Shipping</option>
																	<option value="5" @if($emailtemplate->template_type == 5) selected @endif>Order Track</option>
																	<option value="6" @if($emailtemplate->template_type == 6) selected @endif>Forgot Password</option>
																	<option value="7" @if($emailtemplate->template_type == 7) selected @endif>Enquiry</option>
																	<option value="8" @if($emailtemplate->template_type == 8) selected @endif>Newsletter</option>
																	<option value="9" @if($emailtemplate->template_type == 9) selected @endif>Contact Us</option>
																	<option value="10" @if($emailtemplate->template_type == 10) selected @endif>Quotation</option>
																	<option value="11" @if($emailtemplate->template_type == 11) selected @endif>Feedback</option>
																	<option value="12" @if($emailtemplate->template_type == 12) selected @endif>Order Collection Information</option>
																	<option value="13" @if($emailtemplate->template_type == 13) selected @endif>Order Status Update</option>
																	<option value="14" @if($emailtemplate->template_type == 14) selected @endif>Order Delivery Status Update</option>
																	<option value="15" @if($emailtemplate->template_type == 15) selected @endif>Q&A Form</option>
																	<option value="16" @if($emailtemplate->template_type == 16) selected @endif>Order Success</option>
																	<option value="17" @if($emailtemplate->template_type == 17) selected @endif>Order Cancelled</option>
																</select>
															</div>
														</div>
													</div> 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Template Name <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="templatename" class="form-control" name="templatename" placeholder="Template Name" value = "{{ $emailtemplate->templatename }}" required>
                                                            </div>
                                                        </div>
                                                    </div>       
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Subject <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="subject" class="form-control" name="subject" placeholder="Subject" value = "{{ $emailtemplate->subject }}"  required> 
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Content</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <textarea name="content" class="form-control" id = "editor">{{ $emailtemplate->content }}</textarea>
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
                                                            <option value="0" @if($emailtemplate->status == 0) selected @endif >In-Active</option>
                                                                <option value="1" @if($emailtemplate->status == 1) selected @endif>Active</option>
                                                            </select>    
                                                        </div>


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

<!--script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script-->

<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<script>
     CKEDITOR.replace( 'editor' ); 
</script>