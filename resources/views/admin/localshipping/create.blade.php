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
                            <h2 class="content-header-title float-left mb-0">Manage Local Shipping Method</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/local_shipping_methods') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                       <form class="form form-horizontal" name="local_shipping_methods" id="local_shipping_methods" method = "post" action="{{ url('/admin/local_shipping_methods') }}" enctype="multipart/form-data">                                               
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Delivery Type <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="EnName" class="form-control" name="EnName" placeholder="Delivery Type" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Hint</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="Hint" class="form-control" name="Hint" placeholder="Hint">
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
                                                                <input type="text" id="PriceRange5" class="form-control" name="PriceRange5" placeholder="Price">
                                                            </div>
                                                            <div class="col-md-2">
                                                                 <input type="text" id="PriceRange15" class="form-control" name="PriceRange15" placeholder="Price">
                                                            </div>
															<div class="col-md-2">
                                                                 <input type="text" id="PriceRange30" class="form-control" name="PriceRange30" placeholder="Price">
                                                            </div>
															<div class="col-md-2">
                                                                 <input type="text" id="PriceRangeAbove30" class="form-control" name="PriceRangeAbove30" placeholder="Price">
                                                            </div>															
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Free Shipping Available ? (if free shipping avilable on this method )</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="checkbox" id="FreeShipAvailable" name="FreeShipAvailable"  value = "1">
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Min cart amount for free shipping</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="FreeShipCost" class="form-control" name="FreeShipCost" placeholder="Min cart amount for free shipping">
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
						</section>
                    </div>            


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')
