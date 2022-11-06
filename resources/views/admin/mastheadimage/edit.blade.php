@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
}
	.geeks { 
		width: 80%; 
		/*height: 100px; */
		overflow: hidden; 
		margin: 0 auto; 
	} 
	
	.geeks img { 
		width: 100%; 
		transition: 0.5s all ease-in-out; 
	} 
	
	.geeks:hover img { 
		transform: scale(1.5); 
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
                            <h2 class="content-header-title float-left mb-0">Manage Masthead</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/banner_master') }}" class="btn " tabindex="0" aria-controls="banner_master">
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
                                    <form class="form form-horizontal" name="banner_master" id="banner_master" method = "post" action="{{ url('/admin/banner_master/'.$mastheadimage->ban_id) }}" enctype="multipart/form-data">                                            			
                                    <input type="hidden" name="id" value="{{ $mastheadimage->ban_id }}">
                                    <input type="hidden" name="_method" value="PUT">
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Banner Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ban_name" class="form-control" name="ban_name" placeholder="Banner Name"  value="{{ $mastheadimage->ban_name }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Masthead Images Link</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="ban_link" class="form-control" name="ban_link" placeholder="Masthead Images Link" value="{{ $mastheadimage->ban_link }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Masthead Image (Size: 1920px x 225px)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class= "custom-file">
                                                              <input type="file" name="EnBanimage" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                                              @if($mastheadimage->EnBanimage != '') 
                                                              <div class="geeks"> 
                                                              <img src="{{ url('/uploads/bannermaster/'.$mastheadimage->EnBanimage) }}" alt="{{ $mastheadimage->ban_name }}">
                                                              </div>
                                                              @endif			
                                                              <input type="hidden" name="ExistEnBanimage" value="{{ $mastheadimage->EnBanimage }}">
                                                              <!--div class="imageOutput"></div-->
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Masthead Image for Mobile (Size: 1920px x 450px)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class= "custom-file">
                                                              <input type="file" name="EnBanMobileimage" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                                              @if($mastheadimage->EnBanMobileimage != '') 
                                                              <div class="geeks"> 
                                                              <img src="{{ url('/uploads/bannermaster/'.$mastheadimage->EnBanMobileimage) }}" alt="{{ $mastheadimage->ban_name }}">
                                                              </div>
                                                              @endif			
                                                              <input type="hidden" name="ExistEnBanMobileimage" value="{{ $mastheadimage->EnBanMobileimage }}">
                                                              <div class="imageOutput"></div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Caption</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <textarea id="editor" class="form-control" name="ban_caption" placeholder="Type page content ..." >{!! $mastheadimage->ban_caption !!}</textarea>
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
                                                            <option value="0" @if($mastheadimage->ban_status == 0) selected @endif>In-Active</option>
                                                            <option value="1" @if($mastheadimage->ban_status == 1) selected @endif>Active</option>
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
                $images.html('<img src="'+ e.target.result+'" width="50%"/>')
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
