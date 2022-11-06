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
                            <h2 class="content-header-title float-left mb-0">Manage Page Content</h2>                            
                        </div>	
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group">
								<a href="{{ url('/admin/static_pages') }}" class="btn " tabindex="0" aria-controls="static_pages"><span><i class="feather icon-arrow-left"></i> Back</span></a> 
								</div>
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
                                    <h4 class="card-title">Add</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                    <form class="form form-horizontal" name="static_pages" id="static_pages" method = "post" action="{{ url('/admin/static_pages') }}" enctype="multipart/form-data">                                            
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Page Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="EnTitle" class="form-control" name="EnTitle" placeholder="Name" required>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Menu notes</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <textarea name="ShortDesc" class="form-control" id = "ShortDesc" ></textarea> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Parent Type <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <select name="parent_id" class="form-control" id =  "parent_id" required>
                                                                               <option value="0"  selected="selected">Top Level</option>
                                                                              @if(count($pagecontent) > 0)						

																				@foreach($pagecontent as $page)
																				
																					<option value="{{ $page->Id }}">{{ $page->EnTitle }}</option>
																				@endforeach							

																				@endif	
																		  </select>
																	</div>
																</div>
															</div>
															<div class="col-12">
																<div class="form-group row">
																	<div class="col-md-4">
                                                                <span>Page URL <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="UniqueKey" class="form-control" name="UniqueKey" placeholder="Page URL" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Menu Type <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <select name="menu_type" class="form-control" id = "menu_type" required>
																	<option value="1">Not in Menu</option>
                                                                    <option value="2">Main and Footer Menu</option>
                                                                    <option value="3">Sub Menu</option>
                                                                    <option value="4">Main Menu</option>
                                                                    <option value="5">Footer Menu</option>
																	<option value="6">Footer Bottom Menu</option>
                                                                  </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Banner Type <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <input type="radio" name="banner_type" value="1" />&nbsp;Flash &nbsp;&nbsp;
                                                            <input type="radio"  name="banner_type" value="2" checked="checked" />&nbsp;Image
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Banner Image <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class= "custom-file">
                                                              <input type="file" name="banner_image" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                                            </div>
                                                            <div class="imageOutput"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Page Content</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="editor" class="form-control" name="ChContent" placeholder="Type page content ..." ></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Page Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_title" class="form-control" name="meta_title" placeholder="Meta Title" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Keywords <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_keywords" class="form-control" name="meta_keywords" placeholder="Meta Keywords" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Descriptions <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="meta_description" class="form-control" name="meta_description" placeholder="Meta Keywords" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <select name="display_status" class="form-control" id = "display_status" required>
                                                                  <option value="0" >In-Active</option>
                                                                  <option value="1" selected >Active</option>
                                                               </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Rights to edit <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <select name="rights_to_visible" class="form-control" id = "rights_to_visible" required>
                                                                <option value = "0">Select Rights to edit</option>
                                                                <option value="1">Superadmin Only</option>
                                                                <option value="2" >hardwarecityonline</option>  
                                                                <option value="5" >subadmin</option>  
                                                                <option value="6" >pioneer</option>  
                                                                <option value="8" >superstore</option>  
                                                                <option value="9" >cckadmin</option>  
                                                               </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                   </div>
												   <input type = "hidden" name="display_order" value = "1">
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
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<script>
	CKEDITOR.config.allowedContent = true;
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
                $images.html('<img src="'+ e.target.result+'" />')
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
