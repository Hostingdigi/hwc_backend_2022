@include('admin.includes.header')
<body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="semi-dark-layout">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.sidemenu')
    <!-- BEGIN: Header-->
    <div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Local Shipping Method</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="shippingmethod" id="shippingmethod" method = "post" action="{{ url('/admin/country/'.$country->id ) }}" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="{{ $Local_shipping_method->id }}">
											<input type="hidden" name="_method" value="PUT">
                                            {{ csrf_field() }}
                                           
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Type</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="delivery_type" class="form-control" name="delivery_type" value = "{{ $Local_shipping_method->EnName }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Hint</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="hint" class="form-control" name="hint" value = "{{ $Local_shipping_method->Hint }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Size & Weight Range</span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span>Max 0.8m & 5Kg</span>                                                                
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span>Max 1.2m & 15Kg</span>                                                                
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span>Max 1.8m & 30Kg</span>                                                                
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span>Above 30Kg</span>                                                                
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price (S$)</span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" id="PriceRange1" class="form-control" name="PriceRange1" value = "{{ $Local_shipping_method->PriceRange1 }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" id="PriceRange2" class="form-control" name="PriceRange2" value = "{{ $Local_shipping_method->PriceRange2 }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" id="PriceRange5" class="form-control" name="PriceRange5" value = "{{ $Local_shipping_method->PriceRange5 }}" required>
                                                            </div>                                                        
                                                            <div class="col-md-2">
                                                                <input type="text" id="PriceRange10" class="form-control" name="PriceRange10" value = "{{ $Local_shipping_method->PriceRange10 }}" required>
                                                            </div>
                                                        </div>                                                        
                                                    </div> 

                                                    <div class="col-12">
													<div class="form-group row"> 
														<div class="col-md-4">
														    <span>Free Shipping Available?</span>
                                                                (if free shipping avilable on this method )</span> 
														</div>
														<div class="col-md-8">
														    <input type="checkbox" id="FreeShipAvailable" class="form-control" name="FreeShipAvailable" required> 
														</div>
													</div>
													</div>
													<div class="col-12">
													<div class="form-group row"> 
														<div class="col-md-4">
														    <span>Min cart amount for free shipping*</span> 
														</div>
														<div class="col-md-8">
														    <input type="text" id="FreeShipCost" class="form-control" value = "{{ $Local_shipping_method->FreeShipCost }}" name="FreeShipCost" required> 
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
                                                                <option value="1" @if($Local_shipping_method->status == 1) selected @endif >Active</option>
																<option value="0" @if($Local_shipping_method->status == 0) selected @endif >In-Active</option>
                                                            </select>    
                                                        </div>


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

<!--script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script-->
