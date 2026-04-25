@include('admin.includes.header')
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
                            <h2 class="content-header-title float-left mb-0">Manage Shipping Methods</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/shipping_methods') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                       <form class="form form-horizontal" name="addcountry" id="addcountry" method = "post" action="{{ url('/admin/shipping_methods/'.$shippingmethods->Id) }}" enctype="multipart/form-data">			
										<input type="hidden" name="id" value="{{ $shippingmethods->Id }}">			
										<input type="hidden" name="_method" value="PUT">                                               
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Title <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="EnName" class="form-control" name="EnName" placeholder="Title" value="{{ $shippingmethods->EnName }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Alias URL</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="UniqueKey" class="form-control" name="UniqueKey" value="{{ $shippingmethods->UniqueKey }}" placeholder="Alias URL">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Flat Price <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="Price" value="{{ $shippingmethods->Price }}" class="form-control" name="Price" placeholder="Price" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Free Shipping Available ?</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="checkbox" name="FreeShipAvailable" value="1" @if($shippingmethods->FreeShipAvailable == 1) checked @endif> (if free shipping avilable on this method )
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Min cart amount for free shipping</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                 <input type="text" id="FreeShipCost" class="form-control" name="FreeShipCost" placeholder="Free Ship Cost" value="{{ $shippingmethods->FreeShipCost }}">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Shipping Method For</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                 <select name="shipping_type" class="form-control">			
																	 <option value="0" @if($shippingmethods->shipping_type == 0) selected @endif>Local</option>                
																	<option value="1" @if($shippingmethods->shipping_type == 1) selected @endif>International</option>                              
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
                                                                 <select name="Status" class="form-control">			
																	 <option value="0" @if($shippingmethods->Status == 0) selected @endif>In-Active</option>                
																	<option value="1" @if($shippingmethods->Status == 1) selected @endif>Active</option>                              
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
							
                        </div>
						</section>
                    </div>
                


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')


