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

        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
       <div class="content-header row">

                <div class="content-header-left col-md-12 col-12 mb-2">

                    <div class="row breadcrumbs-top">

                        <div class="col-8">

                            <h2 class="content-header-title float-left mb-0">Manage Products</h2>                            

                        </div>

						<div class="col-4" style="float:right; text-align:right;">

							<div class="dt-buttons btn-group"><a href="{{ url('/admin/products') }}" class="btn " tabindex="0" aria-controls="Products">

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

                                    <h4 class="card-title">Add New Product</h4>

                                </div>

                                <div class="card-content">

                                    <div class="card-body">

                                        <form class="form form-horizontal" name="addproduct" id="addproduct" method = "post" action="{{ url('/admin/products') }}" enctype="multipart/form-data">

                                            <input type="hidden" name="DisplayOrder" value="{{ $displayorder }}">

                                            {{ csrf_field() }}

                                            <div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Name <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea name="EnName" class="form-control" required></textarea>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>URL</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea name="UniqueKey" class="form-control"></textarea>

                                                            </div>

                                                        </div>

                                                    </div>                                                   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Code <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">
																<textarea name="ProdCode" class="form-control" required></textarea>			
                                                            </div>

                                                        </div>

                                                    </div>  

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Category <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                               <select name="Types" class="form-control" required>	
																<option value="">Select Category</option>
																@if(count($categories) > 0)						

																	@foreach($categories as $cate)							

																	<option value="{{ $cate->TypeId }}">{{ $cate->EnName }}</option>							

																	@php								

																	$subcats = [];								

																	$subcats = \App\Models\Category::where('ParentLevel','=',$cate->TypeId)->orderBy('EnName', 'ASC')->get();
																	@endphp								

																	@if(count($subcats) > 0)								

																	@foreach($subcats as $subcat)									

																	<option value="{{ $subcat->TypeId }}">&nbsp;&nbsp >> {{ $subcat->EnName }}</option>		@php
																				$subsubcats = [];
																				$subsubcats = \App\Models\Category::where('ParentLevel','=',$subcat->TypeId)->orderBy('EnName', 'ASC')->get();
																				@endphp
																				@if(count($subsubcats) > 0)
																					@foreach($subsubcats as $subsubcat)
																						<option value="{{ $subsubcat->TypeId }}">&nbsp;&nbsp;&nbsp;&nbsp; >>> {{ $subsubcat->EnName }}</option>
																					@endforeach
																				@endif						

																	@endforeach							

																	@endif												

																	@endforeach					

																	@endif  
															   </select>

                                                            </div>

                                                        </div>

                                                    </div>    

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Brand</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <select name="Brand" class="form-control">	

																<option value="0">No Brand</option>            		

																	@if(count($brands) > 0)						

																		@foreach($brands as $brand)							

																			<option value="{{ $brand->BrandId }}">{{ $brand->EnName }}</option>

																		@endforeach					

																	@endif			    

																</select>

                                                            </div>

                                                        </div>                                              
													</div>
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Standard Price <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="Price" class="form-control" name="Price" placeholder="Standard Price" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Cost Price <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="StandardPrice" class="form-control" name="StandardPrice" placeholder="Cost Price" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Vendor </span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea name="Vendor" class="form-control"></textarea> 

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>POC</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="Supplier" class="form-control" name="Supplier" placeholder="POC">

                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Gebiz item ?</span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="checkbox" name="gebiz_item" value="1">  

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Color</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="Color" class="form-control" name="Color" placeholder="Color">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Sizes</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="Size" class="form-control" name="Size" placeholder="Size">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Specs</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea id="Specs" class="form-control editor" name="Specs" placeholder="Specs"></textarea>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Dimension</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="Dimension" class="form-control" name="Dimension" placeholder="Dimension">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>MOQ</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="MOQ" class="form-control" name="MOQ" placeholder="MOQ">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>UNSPSC Code</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="unspsc" class="form-control" name="unspsc" placeholder="UNSPSC Code">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Quantity <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="Quantity" class="form-control" required name="Quantity" placeholder="Quantity">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Customer daily purchase limit <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="cust_qty_per_day" required class="form-control" name="cust_qty_per_day" placeholder="Customer daily purchase limit">

                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Shippping Box Size</span>

                                                            </div>

                                                            <div class="col-md-8">

																<select name="ShippingBox" class="form-control">              

																	   <option value="0" selected="">Select</option>					

																	   <option value="XXS">XXS (L33.7cm X W18.2cm X H10cm)</option>			

																		<option value="XS">XS (L33.6cm X W32cm X H5.2cm)</option>			

																		<option value="S">S (L33.7cm X W32.2cm X H18cm)</option>				

																		<option value="M">M (L33.7cm X W32.2cm X H34.5cm)</option>					

																		<option value="L">L (L41.7cm X W35.9cm X H36.9cm)</option>				

																		<option value="XL">XL (L48.1cm X W40.4cm X H38.9cm) </option>				

																		<option value="XXL">XXL (L54.1cm X W44.4cm X H40.9cm)</option>				

																		<option value="P">P (Pallet Size)</option>               

																</select>

																	

                                                            </div>

                                                        </div>

                                                    </div>	

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Weight(in Kg)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="Weight" class="form-control" name="Weight" placeholder="Weight">

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Tags</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="ProdTags" class="form-control" name="ProdTags" placeholder="Tags">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input imageUpload" id="Image" name="Image">

                                                                <label class="custom-file-label" for="Image">Choose Image File</label>

                                                            </div>
																<div class="imageOutput"></div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Large Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input largeimageUpload" id="LargeImage" name="LargeImage">

                                                                <label class="custom-file-label" for="LargeImage">Choose Large Image File</label>

                                                            </div>
																<div class="largeimageOutput"></div>

                                                            </div>

                                                        </div>

                                                    </div> 

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Mobile Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input imageUpload" id="MobileImage" name="MobileImage">

                                                                <label class="custom-file-label" for="Image">Choose Image File</label>

                                                            </div>
																

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Mobile Large Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input largeimageUpload" id="MobileLargeImage" name="MobileLargeImage">

                                                                <label class="custom-file-label" for="LargeImage">Choose Large Image File</label>

                                                            </div>
																

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>TDS (Upload PDF & Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input" id="Tds" name="Tds">

                                                                <label class="custom-file-label" for="Tds">Choose TDS PDF File</label>

                                                            </div>

                                                            </div>

                                                        </div>

                                                    </div>          

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SDS (Upload PDF & Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input" id="Sds" name="Sds">

                                                                <label class="custom-file-label" for="Sds">Choose SDS PDF File</label>

                                                            </div>

                                                            </div>

                                                        </div>

                                                    </div>   
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Video (embeded code only)</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Video" class="form-control"></textarea>       

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Featured Product ?</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="checkbox" id="IsFeatured" name="IsFeatured" value = "1">

                                                            </div>

                                                        </div>

                                                    </div>  
                                                   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Promotion ?</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="checkbox" id="IsPromotion" name="IsPromotion" value = "1">

                                                            </div>

                                                        </div>

                                                    </div>      

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Overseas Shipping Available ?</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="checkbox" id="IsOverseasShippingTrue" name="IsOverseasShippingTrue" value = "1">

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Short Description</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <textarea id="EnShortDesc" class="form-control" name="EnShortDesc" placeholder="Type page content ..." ></textarea>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Details</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea id="editor" class="form-control editor" name="EnInfo" placeholder="Content"></textarea>                                                               

                                                            </div>

                                                        </div>

                                                    </div>                                   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Meta Title</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea name="MetaTitle" class="form-control"></textarea> 

                                                            </div>

                                                        </div>

                                                    </div>

                                                    

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Meta Keyword</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea name="MetaKey" class="form-control"></textarea> 

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Meta Description</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea name="MetaDesc" class="form-control"></textarea>

                                                            </div>

                                                        </div>

                                                    </div>     
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SEO Star Script</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                          <textarea name="seo_star_script" class="form-control"></textarea> 

                                                            </div>

                                                        </div>

                                                    </div>													

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                            <span>Status</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <select name="ProdStatus" class="form-control">

																<option value="0">In-Active</option>               

																<option value="1" selected>Active</option>  
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

                $images.html('<img src="'+ e.target.result+'" width="300"/>')

            }

            reader.readAsDataURL(this);

        });

        

    }

}

$largeimages = $('.largeimageOutput')



$(".largeimageUpload").change(function(event){

    largereadURL(this);

});



function largereadURL(input) {



    if (input.files && input.files[0]) {

        

        $.each(input.files, function() {

            var reader = new FileReader();

            reader.onload = function (e) {           

                $largeimages.html('<img src="'+ e.target.result+'" width="300"/>')

            }

            reader.readAsDataURL(this);

        });

        

    }

}

   

</script>