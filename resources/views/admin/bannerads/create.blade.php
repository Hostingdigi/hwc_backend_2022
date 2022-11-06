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

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Advertisement Banner</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/banner_ads') }}" class="btn " tabindex="0" aria-controls="banner_master">
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
                                <div class="card-header">
                                    <h4 class="card-title">Add New</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                    <form class="form form-horizontal" name="promotions" id="promotions" method = "post" action="{{ url('/admin/banner_ads') }}" enctype="multipart/form-data" accept-charset="UTF-8">                                            
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Banner Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ban_name" class="form-control" name="ban_name" placeholder="Banner Name" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Banner Link</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ban_link" class="form-control" name="ban_link" placeholder="Banner Link">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Banner Image</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class= "custom-file">
                                                              <input type="file" name="EnBanimage" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              
                                                            </div>
                                                            <div class="imageOutput"></div>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                    
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Video </span>
                                                            </div>
                                                            <div class="col-md-8">
															<textarea name="Video" id="Video" class="form-control"></textarea>
																	
                                                            </div>
                                                        </div>
                                                    </div>

													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Ad Display Page</span>
                                                            </div>
                                                            <div class="col-md-8">
																<select name="PageId" class="form-control">
																	<option value="">Select Page</option>
																	@if($pages)
																		@foreach($pages as $page)
																		<option value="{{ $page->UniqueKey }}">{{ $page->EnTitle }}</option>
																		@endforeach
																	@endif	
																</select>
															</div>
					                                    </div>
                                                    </div>	
															<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Display Order</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="display_order" class="form-control" name="display_order" value="0" placeholder="Display Order">
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <select name="ban_status" class="form-control">
                                                              <option value="0" >In-Active</option>
                                                              <option value="1" selected>Active</option>
                                                                        
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1" name="submit_action">Save</button>
                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
                                                    </div>
                                                  </div>
                                                  </div>
                                          </form>
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

<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<script>
	 CKEDITOR.replace( 'editor' );
   
    $images = $('.imageOutput')

$(".imageUpload").change(function(event){
    readURL(this);
});

function readURL(input) {

    if (input.files && input.files[0]) {
        
        $.each(input.files, function() {
            var reader = new FileReader();
            reader.onload = function (e) {           
                $images.html('<img src="'+ e.target.result+'" width="200" />')
            }
            reader.readAsDataURL(this);
        });
        
    }
}

</script>
<!--script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script-->
