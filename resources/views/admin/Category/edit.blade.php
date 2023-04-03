@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
}
.geeks { 
		width:300px;
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
                            <h2 class="content-header-title float-left mb-0">Manage Category</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/category') }}" class="btn " tabindex="0" aria-controls="banner_master">
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
                                   <form class="form form-horizontal" name="addcategory" id="addcategory" method = "post" action="{{ url('/admin/category/'.$category->TypeId) }}" enctype="multipart/form-data">			
									<input type="hidden" name="id" value="{{ $category->TypeId }}">			
									<input type="hidden" name="_method" value="PUT">              
									{{ csrf_field() }}	
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Category <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <textarea name="EnName" required class="form-control" id = "EnName">{!! $category->EnName !!}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Page URL</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="UniqueKey" class="form-control" name="UniqueKey" value="{{ $category->UniqueKey }}" placeholder="Page URL">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Image (295px x 216px)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class= "custom-file">
                                                              <input type="file" name="Image" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>  
															  <div class="geeks">	
																@if($category->Image != '') 				
																	<img src="{{ url('/uploads/category/'.$category->Image) }}" width="150" height="150">			
																@endif   
														      </div>                                                            
                                                            </div>
															<input type="hidden" name="ExistImage" value="{{ $category->Image }}">
                                                            <!--div class="imageOutput"></div-->	
                                                            </div>
                                                        </div>
                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Mobile Image (295px x 216px)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class= "custom-file">
                                                              <input type="file" name="MobileImage" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>  
															  <div class="geeks">	
																@if($category->MobileImage != '') 				
																	<img src="{{ url('/uploads/category/'.$category->MobileImage) }}" width="150" height="150">			
																@endif   
														      </div>                                                            
                                                            </div>
															<input type="hidden" name="ExistMobileImage" value="{{ $category->MobileImage }}">
                                                            <div class="imageOutput"></div>															
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Level</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <select name="ParentLevel" class="form-control" id = "ParentLevel">            
                                                                   <option value="0" @if($category->ParentLevel == 0) selected @endif>Top Level</option>			    @if(count($categories) > 0)					@foreach($categories as $cate)						<option value="{{ $cate->TypeId }}" @if($category->ParentLevel == $cate->TypeId) selected @endif>{{ $cate->EnName }}</option>						@php							$subcats = [];							$subcats = \App\Models\Category::where('ParentLevel','=',$cate->TypeId)->orderBy('EnName', 'ASC')->get();						@endphp							@if(count($subcats) > 0)							@foreach($subcats as $subcat)								<option value="{{ $subcat->TypeId }}" @if($category->ParentLevel == $subcat->TypeId) selected @endif>&nbsp;&nbsp>>{{ $subcat->EnName }}</option>								@php									$subsubcats = [];									$subsubcats = \App\Models\Category::where('ParentLevel','=',$subcat->TypeId)->orderBy('EnName', 'ASC')->get();								@endphp									@if(count($subsubcats) > 0)									@foreach($subsubcats as $subsubcat)										<option value="{{ $subsubcat->TypeId }}" @if($category->ParentLevel == $subsubcat->TypeId) selected @endif>&nbsp;&nbsp>>{{ $subsubcat->EnName }}</option>									@endforeach								@endif							@endforeach						@endif											@endforeach				@endif                                    
                                                                    </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Details</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                                <textarea name="Details" class="form-control" id = "editor">{!! $category->Details !!}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Is discount applicable ?</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="checkbox" id="dis_type" class="" name="dis_type" value="1" @if($category->dis_type == 1) checked @endif >
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Is Offer Category ?</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="checkbox" id="offerCategory" class="" name="offerCategory" value="1" @if($category->offerCategory == 1) checked @endif>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Page Title</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea name="meta_title" class="form-control" id = "meta_title">{!! $category->meta_title !!}</textarea>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Keywords</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea name="meta_keywords" class="form-control" id = "meta_keywords">{!! $category->meta_keywords !!}</textarea>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Descriptions</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea name="meta_description" class="form-control" id = "meta_description">{!! $category->meta_description !!}</textarea>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Star Script</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea name="seo_star_script"  class="form-control" id = "seo_star_script">{!! $category->seo_star_script !!}</textarea>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <select name="TypeStatus" class="form-control">
                                                               <option value="0" @if($category->TypeStatus == 0) selected @endif>In-Active</option>
															   <option value="1" @if($category->TypeStatus == 1) selected @endif>Active</option>  
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

$('#EnName').keyup(function() {
	var title = $('#EnName').val();
	title = title.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
	$('#UniqueKey').val(title.toLowerCase());
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