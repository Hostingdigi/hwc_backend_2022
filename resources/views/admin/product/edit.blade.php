@include('admin.includes.header')

<style>

.ck-editor__editable {

    min-height: 300px;

}

.geeks { 

		width: 300px; 

		

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

@php
if($adminrole ==5)
    $field_display = "readonly";
else
    $field_display = "";
@endphp


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

                                    <h4 class="card-title">Update Product</h4>

                                </div>

                                <div class="card-content">

                                    <div class="card-body">

                                   <form class="form form-horizontal" name="products_frm" id="products_frm" method = "post" action="{{ url('/admin/products/'.$product->Id) }}" enctype="multipart/form-data">			

								   <input type="hidden" name="id" value="{{ $product->Id }}">			

								   <input type="hidden" name="_method" value="PUT">  

                                            {{ csrf_field() }}

                                            <div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Name <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

																
<textarea name="EnName" id="EnName" class="form-control" required {{$field_display}}>{{ $product->EnName }}</textarea>

                                                            </div>

                                                        </div>

                                                    </div> 

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>URL alias </span>

                                                            </div>

                                                            <div class="col-md-8">

																
<textarea name="UniqueKey" id="UniqueKey" class="form-control" required {{$field_display}}>{{ $product->UniqueKey }}</textarea>

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Code</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="ProdCode" class="form-control" required {{$field_display}}>{{ $product->ProdCode }}</textarea>

                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Barcode <br>(Use comma separater for multiple values)</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="barcode" class="form-control" >{{ $product->barcode }}</textarea>

                                                            </div>

                                                        </div>

                                                    </div>
                                                    
                                                    @if($adminrole !=5)
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Category <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

																<select name="Types" class="form-control" {{$field_display}} required>					

																@if(count($categories) > 0)						

																	@foreach($categories as $cate)							

																		<option value="{{ $cate->TypeId }}" @if($product->Types == $cate->TypeId) selected @endif>{{ $cate->EnName }}</option>							

																		@php								

																			$subcats = [];								

																			$subcats = \App\Models\Category::where('ParentLevel','=',$cate->TypeId)->orderBy('EnName', 'ASC')->get();
																		@endphp								

																		@if(count($subcats) > 0)								

																			@foreach($subcats as $subcat)									

																				<option value="{{ $subcat->TypeId }}" @if($product->Types == $subcat->TypeId) selected @endif>&nbsp;&nbsp>>{{ $subcat->EnName }}</option>
																				
																				@php
																				$subsubcats = [];
																				$subsubcats = \App\Models\Category::where('ParentLevel','=',$subcat->TypeId)->orderBy('EnName', 'ASC')->get();
																				@endphp
																				@if(count($subsubcats) > 0)
																					@foreach($subsubcats as $subsubcat)
																						<option value="{{ $subsubcat->TypeId }}" @if($product->Types == $subsubcat->TypeId) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp; >>> {{ $subsubcat->EnName }}</option>
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

																<select name="Brand" class="form-control" {{$field_display}}>	

																<option value="0" selected="selected">No Brand</option>            		

																	@if(count($brands) > 0)						

																		@foreach($brands as $brand)							

																			<option value="{{ $brand->BrandId }}" @if($product->Brand == $brand->BrandId) selected @endif>{{ $brand->EnName }}</option>

																		@endforeach					

																	@endif			    

																</select>

                                                            </div>

                                                        </div>

                                                    </div>

                                                   

													 <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Standard Price</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="Price" class="form-control" name="Price" placeholder="Price" value="{{ $product->Price }}" required>

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Cost Price</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="StandardPrice" class="form-control" name="StandardPrice" placeholder="Price" value="{{ $product->StandardPrice }}" required>

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Vendor</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Vendor" class="form-control" >{{ $product->Vendor }}</textarea>

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>POC</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Supplier" class="form-control">{{ $product->Supplier }}</textarea>  

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Gebiz item ?</span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="checkbox" name="gebiz_item" value="1" @if($product->gebiz_item == 1) checked @endif>  

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Color</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Color" class="form-control">{{ $product->Color }}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Sizes</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Size" class="form-control">{{ $product->Size }}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Specs</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Specs" id="Specs" class="form-control editor">{{ $product->Specs }}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Dimension</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Dimension" class="form-control">{{ $product->Dimension }}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>													

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>MOQ</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="MOQ" class="form-control">{{ $product->MOQ }}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>UNSPSC Code</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="unspsc" class="form-control">{{ $product->unspsc }}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Quantity <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="text" name="Quantity" required value="{{ $product->Quantity }}" class="form-control">     

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Customer daily purchase limit <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="text" name="cust_qty_per_day" required value="{{ $product->cust_qty_per_day }}" class="form-control"> 

																	

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

																	   <option value="XXS" @if($product->ShippingBox == 'XXS') selected @endif>XXS (L33.7cm X W18.2cm X H10cm)</option>			

																		<option value="XS" @if($product->ShippingBox == 'XS') selected @endif>XS (L33.6cm X W32cm X H5.2cm)</option>			

																		<option value="S" @if($product->ShippingBox == 'S') selected @endif>S (L33.7cm X W32.2cm X H18cm)</option>				

																		<option value="M" @if($product->ShippingBox == 'M') selected @endif>M (L33.7cm X W32.2cm X H34.5cm)</option>					

																		<option value="L" @if($product->ShippingBox == 'L') selected @endif>L (L41.7cm X W35.9cm X H36.9cm)</option>				

																		<option value="XL" @if($product->ShippingBox == 'XL') selected @endif>XL (L48.1cm X W40.4cm X H38.9cm) </option>				

																		<option value="XXL" @if($product->ShippingBox == 'XXL') selected @endif>XXL (L54.1cm X W44.4cm X H40.9cm)</option>				

																		<option value="P" @if($product->ShippingBox == 'P') selected @endif>P (Pallet Size)</option>               

																</select>

																	

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Weight(in kg) (ex. 0.5)</span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="text" name="Weight" value="{{ $product->Weight }}" class="form-control"> 

																	

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Tags</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="ProdTags" class="form-control" value="{{ $product->ProdTags }}" name="ProdTags" placeholder="Tags">

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class= "custom-file">

                                                              <input type="file" name="Image" class="custom-file-input imageUpload" /> 

                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              

                                                            </div>

                                                            <div class="imageOutput"></div>

															@if($product->Image != '') 



                                                            <div class="geeks"> 

                                                              <img src="{{ url('/uploads/product/'.$product->Image) }}" alt="{{ $product->EnName }}">

                                                            </div>

                                                            @endif			

                                                            <input type="hidden" name="ExistImage" value="{{ $product->Image }}">

                                                            </div>

                                                        </div>

                                                    </div>
													
													

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Large Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class= "custom-file">

                                                              <input type="file" name="LargeImage" class="custom-file-input largeimageUpload" /> 

                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              

                                                            </div>

                                                            <div class="largeimageOutput"></div>

															@if($product->LargeImage != '') 



                                                            <div class="geeks"> 

                                                              <img src="{{ url('/uploads/product/'.$product->LargeImage) }}" alt="{{ $product->EnName }}">

                                                            </div>

                                                            @endif			

                                                            <input type="hidden" name="ExistLargeImage" value="{{ $product->LargeImage }}">

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Mobile Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class= "custom-file">

                                                              <input type="file" name="MobileImage" class="custom-file-input imageUpload" /> 

                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              

                                                            </div>

                                                            

															@if($product->MobileImage != '') 



                                                            <div class="geeks"> 

                                                              <img src="{{ url('/uploads/product/'.$product->MobileImage) }}" alt="{{ $product->EnName }}">

                                                            </div>

                                                            @endif			

                                                            <input type="hidden" name="ExistMobileImage" value="{{ $product->MobileImage }}">

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Mobile Large Picture (400px x 400px)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class= "custom-file">

                                                              <input type="file" name="MobileLargeImage" class="custom-file-input largeimageUpload" /> 

                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>                                                              

                                                            </div>

                                                            <!--div class="largeimageOutput"></div-->

															@if($product->MobileLargeImage != '') 

                                                            <div class="geeks"> 

                                                              <img src="{{ url('/uploads/product/'.$product->MobileLargeImage) }}" alt="{{ $product->EnName }}">

                                                            </div>

                                                            @endif			

                                                            <input type="hidden" name="ExistMobileLargeImage" value="{{ $product->MobileLargeImage }}">

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>TDS (Upload PDF &amp; Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class= "custom-file">

                                                              <input type="file" name="Tds" class="custom-file-input" /> 

                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>

															  </div>

															  @if($product->Tds != '') 

																<a href="{{ url('/uploads/product/'.$product->Tds) }}" target="_blank">

																<img src="{{ url('/images/view_img.gif') }}"></a>

															  @endif			

																<input type="hidden" name="ExistTds" value="{{ $product->Tds }}">

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SDS (Upload PDF &amp; Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class= "custom-file">

                                                              <input type="file" name="Sds" class="custom-file-input" /> 

                                                              <label class="custom-file-label" for="inputGroupFile01">Choose file</label>

                                                            </div>    

															@if($product->Sds != '') 

															<a href="{{ url('/uploads/product/'.$product->Sds) }}" target="_blank">

															<img src="{{ url('/images/view_img.gif') }}"></a>

															@endif			

															<input type="hidden" name="ExistSds" value="{{ $product->Sds }}"> 															

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Video (embeded code only)</span>

                                                            </div>

                                                            <div class="col-md-8">

																<textarea name="Video" class="form-control">{!! $product->Video !!}</textarea>       

                                                            </div>

                                                        </div>

                                                    </div>	

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Featured Product ?</span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="checkbox" name="IsFeatured" value="1" @if($product->IsFeatured == 1) checked @endif>  

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Promotion ?</span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="checkbox" name="IsPromotion" value="1" @if($product->IsPromotion == 1) checked @endif>  

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Overseas Shipping Available ?</span>

                                                            </div>

                                                            <div class="col-md-8">

																<input type="checkbox" name="IsOverseasShippingTrue" value="1" @if($product->IsOverseasShippingTrue == 1) checked @endif>  

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Short Description</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <textarea id="EnShortDesc" class="form-control" name="EnShortDesc" placeholder="Type page content ..." >{!! $product->EnShortDesc !!}</textarea>

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Details</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <textarea id="editor" class="form-control" name="EnInfo" placeholder="Type page content ..." >{!! $product->EnInfo !!}</textarea>

                                                            </div>

                                                        </div>

                                                    </div>   

													

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SEO Meta Title</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                          <textarea name="MetaTitle" class="form-control">{!! $product->MetaTitle !!}</textarea> 

                                                            </div>

                                                        </div>

                                                    </div>	



													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SEO Meta Keywords</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                          <textarea name="MetaKey" class="form-control">{!! $product->MetaKey !!}</textarea> 

                                                            </div>

                                                        </div>

                                                    </div>													

													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SEO Meta Descriptions</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                          <textarea name="MetaDesc" class="form-control">{!! $product->MetaDesc !!}</textarea> 

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

																<option value="0" @if($product->ProdStatus == 0) selected @endif>In-Active</option>                

																<option value="1" @if($product->ProdStatus == 1) selected @endif>Active</option>  </select>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    @endif

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
	CKEDITOR.replace( 'Specs' );

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

                $largeimages.html('<img src="'+ e.target.result+'" width="300" />')

            }

            reader.readAsDataURL(this);

        });

        

    }

}

   

</script>