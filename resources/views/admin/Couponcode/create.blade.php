@include('admin.includes.header')

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
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

                            <h2 class="content-header-title float-left mb-0">Manage Coupon Code</h2>                            

                        </div>

						<div class="col-4" style="float:right; text-align:right;">

							<div class="dt-buttons btn-group"><a href="{{ url('/admin/couponcode') }}" class="btn " tabindex="0" aria-controls="couponcode">

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

                                        <form class="form form-horizontal" name="addcouponcode" id="addcouponcode" method = "post" action="{{ url('/admin/couponcode') }}" enctype="multipart/form-data">

                                            

                                            {{ csrf_field() }}

                                            <div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Coupon Code <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">
                                                                                                                        
                                                                <input type="text" name="coupon_code" class="form-control" required></textarea>

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Customer</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <select name="customer_id" class="form-control select2">	

																<option value="0">Select Customer</option>            		

																	
                                                                @if(count($customers) > 0)						

                                                                    @foreach($customers as $customer)							

                                                                        <option value="{{ $customer->cust_id }}" >{{ $customer->cust_firstname.' '.$customer->cust_lastname.' - '.$customer->cust_email }}</option>

                                                                    @endforeach					

                                                                    @endif			    

																</select>

                                                            </div>

                                                        </div>                                              
													</div>
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Category </span>

                                                            </div>

                                                            <div class="col-md-8">

                                                               <select name="category_id" class="form-control" >	
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

                                                                <select name="brand_id" class="form-control">	

																<option value="0">Select Brand</option>            		

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

                                                                <span>Validity <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="validity" class="form-control pickadate" name="validity" placeholder="Validity" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>No of Times <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="nooftimes" class="form-control" name="nooftimes" placeholder="No of Times" required>

                                                            </div>

                                                        </div>

                                                    </div>
                                                  
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Discount</span>

                                                            </div>

                                                            <div class="col-md-4">

                                                            <span>Discount Type</span>			

                                                            </div>
                                                            <div class="col-md-4">

                                                            <span>Discount Amount</span>			

                                                            </div>
                                                        </div>

                                                    </div>	

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                

                                                            </div>

                                                            <div class="col-md-4">

                                                            <select name="discount_type" class="form-control">              

																	   <option value="1" selected="">Percentage</option>	

																	   <option value="2">Fixed Amount</option>																			
																</select>

                                                            </div>
                                                            <div class="col-md-4">
                                                            <input type="text" id="discount" class="form-control" name="discount" placeholder="Discount Value" required>
                                                            </div>

                                                        </div>

                                                    </div>     
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                            <span>Customer Type</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <select name="customer_type" class="form-control">

                                                                <option value="1" >Individual</option>               
                                                                <option value="2">Overall</option>
																<option value="3">Group Admin</option>	
                                                            </select>


                                                        </div>

                                                        </div>

                                                        </div>                                             										

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                            <span>Status</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <select name="status" class="form-control">

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
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>
$( function() {
    'use strict';
    $('.pickadate').pickadate({
        format: 'yyyy-mm-dd'

    });
});
</script>