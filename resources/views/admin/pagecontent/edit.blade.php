@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 300px;
}
.geeks { 
		width: 150px; 
		/*height: 150px; */
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
                            <h2 class="content-header-title float-left mb-0">Edit Page Content</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/static_pages') }}" class="btn " tabindex="0" aria-controls="static_pages"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                    <form class="form form-horizontal" name="static_pages" id="static_pages" method = "post" action="{{ url('/admin/static_pages/'.$pagecontent->Id) }}" enctype="multipart/form-data">                                            
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id" value="{{ $pagecontent->Id }}">
											<input type="hidden" name="_method" value="PUT"> 
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Page Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="EnTitle" class="form-control" name="EnTitle" placeholder="Name" value = "{{ $pagecontent->EnTitle }}" required>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Menu notes</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <textarea name="ShortDesc" class="form-control" id = "ShortDesc" >{!! $pagecontent->ShortDesc !!}</textarea> 
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
																 <option value="0" @if($pagecontent->parent_id == 0) selected @endif>Top Level</option>
																 @if(count($pagecont) > 0)						

																@foreach($pagecont as $page)
																
																	<option value="{{ $page->Id }}" @if($pagecontent->parent_id == $page->Id) selected @endif >{{ $page->EnTitle }}</option>
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
                                                                <input type="text" id="UniqueKey" class="form-control" name="UniqueKey" placeholder="Page URL" value = "{{ $pagecontent->UniqueKey }}" required>
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
                                                                <option value="1" @if($pagecontent->menu_type == '1') selected @endif>Not in menu</option>
                                                                <option value="2" @if($pagecontent->menu_type == '2') selected @endif>Main and Footer Menu</option>
                                                                <option value="3" @if($pagecontent->menu_type == '3') selected @endif>Submenu</option>
                                                                <option value="4" @if($pagecontent->menu_type == '4') selected @endif>Main menu</option>  
                                                                <option value="5" @if($pagecontent->menu_type == '5') selected @endif>Footer menu</option>   
                                                                <option value="6" @if($pagecontent->menu_type == '6') selected @endif>Footer Bottom menu</option>   
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
                                                            <input type="radio" name="banner_type" value="1" @if($pagecontent->banner_type == 1) checked @endif />&nbsp;Flash &nbsp;&nbsp;
                                                            <input type="radio"  name="banner_type" value="2" @if($pagecontent->banner_type == 2) checked @endif />&nbsp;Image
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
                                                              <input type="file" name="banner_image" class="custom-file-input" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                                            </div>
                                                            @if($pagecontent->banner_image != '') 
                                                            <div class="geeks">	
                                                            <img src="{{ url('/uploads/pagecontent/'.$pagecontent->banner_image) }}" >
                                                            </div>
                                                            @endif
                                                            <input type="hidden" name="exist_banner_image" value="{{ $pagecontent->banner_image }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Page Content</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="editor" class="form-control" name="ChContent" placeholder="Type page content ..." >{!! $pagecontent->ChContent !!}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Page Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_title" class="form-control" name="meta_title" placeholder="Meta Title" value = "{!! $pagecontent->meta_title !!}" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Keywords <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_keywords" class="form-control" name="meta_keywords" value = "{!! $pagecontent->meta_keywords !!}" placeholder="Meta Keywords" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Descriptions <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="meta_description" class="form-control" name="meta_description" placeholder="Meta Keywords" required>{!! $pagecontent->meta_description !!}</textarea>
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
                                                                  <option value="0" @if($pagecontent->display_status == 0) selected @endif>In-Active</option>
                                                                  <option value="1" @if($pagecontent->display_status == 1) selected @endif >Active</option>
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
                                                                <option value="1" @if($pagecontent->rights_to_visible == 1) selected @endif>Superadmin Only</option>
                                                                <option value="2" @if($pagecontent->rights_to_visible == 2) selected @endif>hardwarecityonline</option>  
                                                                <option value="5" @if($pagecontent->rights_to_visible == 5) selected @endif>subadmin</option>  
                                                                <option value="6" @if($pagecontent->rights_to_visible == 6) selected @endif>pioneer</option>  
                                                                <option value="8" @if($pagecontent->rights_to_visible == 8) selected @endif>superstore</option>  
                                                                <option value="9" @if($pagecontent->rights_to_visible == 9) selected @endif>cckadmin</option>   
                                                               </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                   </div>
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" name = "submit_action" class="btn btn-primary mr-1 mb-1">Save</button>
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
