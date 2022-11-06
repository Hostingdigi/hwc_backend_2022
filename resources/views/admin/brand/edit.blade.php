@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 300px;
}
.geeks { 
		width: 300px; 
		height: 300px; 
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
                            <h2 class="content-header-title float-left mb-0">Manage Brands</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/brands') }}" class="btn " tabindex="0" aria-controls="brands">
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
                                    <form class="form form-horizontal" name="addbrand" id="addbrand" method = "post" action="{{ url('/admin/brands/'.$brand->BrandId) }}" enctype="multipart/form-data">									<input type="hidden" name="id" value="{{ $brand->BrandId }}">
									<input type="hidden" name="_method" value="PUT">                                            
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Brand Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
															<textarea name="EnName" class="form-control" placeholder="Brand Name" required>{{ $brand->EnName }}</textarea>                                                                
                                                            </div>
                                                        </div>
                                                    </div>

													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Is this popular brand ?</span>
                                                            </div>
                                                            <div class="col-md-8">
															<select name="PopularBrand" class="form-control" id ="PopularBrand">			
																<option value="0" @if($brand->PopularBrand == 0) selected @endif>No</option>                
																<option value="1" @if($brand->PopularBrand == 1) selected @endif >Yes</option>                             
															</select>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Logo (150px x 140px)</span>
                                                            </div>
                                                            <div class="col-md-8">
															 <div class= "custom-file">
                                                              <input type="file" name="Image" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              
                                                            </div>
                                                            <!--div class="imageOutput"></div-->
															 @if($brand->Image != '') 

                                                            <div class="geeks"> 
                                                              <img src="{{ url('/uploads/brands/'.$brand->Image) }}" alt="{{ $brand->ban_name }}">
                                                            </div>
                                                            @endif			
                                                            <input type="hidden" name="ExistImage" value="{{ $brand->Image }}">
															
															</div>
                                                        </div>
                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Mobile Logo (150px x 140px)</span>
                                                            </div>
                                                            <div class="col-md-8">
															 <div class= "custom-file">
                                                              <input type="file" name="MobileImage" class="custom-file-input imageUpload" /> 
                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              
                                                            </div>
                                                            <div class="imageOutput"></div>
															 @if($brand->MobileImage != '') 

                                                            <div class="geeks"> 
                                                              <img src="{{ url('/uploads/brands/'.$brand->MobileImage) }}" alt="{{ $brand->ban_name }}">
                                                            </div>
                                                            @endif			
                                                            <input type="hidden" name="ExistMobileImage" value="{{ $brand->MobileImage }}">
															
															</div>
                                                        </div>
                                                    </div>
													
													 <div class="col-12">                                                        
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Page URL</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="UniqueKey" class="form-control" value="{{ $brand->UniqueKey }}" name="UniqueKey" placeholder="Page URL">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Caption</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <textarea id="editor" class="form-control" name="Details" placeholder="Type page content ..." >{!! $brand->Details !!}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Is Group Price available ?</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                           <input type="checkbox" name="dis_type" value="1" @if($brand->dis_type == 1) checked @endif> 
                                                            </div>
                                                        </div>
                                                    </div>	
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Exclude Global Discount ?</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                           <input type="checkbox" name="exclude_global_dis" value="1" @if($brand->exclude_global_dis == 1) checked @endif> 
                                                            </div>
                                                        </div>
                                                    </div>	
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Page Title</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                          <textarea name="meta_title" class="form-control">{{ $brand->meta_title }}</textarea> 
                                                            </div>
                                                        </div>
                                                    </div>	

													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Keywords</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                          <textarea name="meta_keywords" class="form-control">{!! $brand->meta_keywords !!}</textarea> 
                                                            </div>
                                                        </div>
                                                    </div>													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Meta Descriptions</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                          <textarea name="meta_description" class="form-control">{!! $brand->meta_description !!}</textarea> 
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>SEO Star Script</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                          <textarea name="seo_star_script" class="form-control">{!! $brand->seo_star_script !!}</textarea> 
                                                            </div>
                                                        </div>
                                                    </div>
													
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <select name="BrandStatus" class="form-control">
                                                              <option value="0" @if($brand->BrandStatus == 0) selected @endif>In-Active</option>                
															<option value="1" @if($brand->BrandStatus == 1) selected @endif>Active</option>    
                                                                        
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
                $images.html('<img src="'+ e.target.result+'" />')
            }
            reader.readAsDataURL(this);
        });
        
    }
}

</script>