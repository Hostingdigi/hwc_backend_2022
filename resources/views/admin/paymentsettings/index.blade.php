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
                            <h2 class="content-header-title float-left mb-0">Payment Configuration</h2>                            
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
                                    <form class="form form-horizontal" name="paymentsettings" id="paymentsettings" method = "post" action="{{ url('/admin/payment_settings/'.$paysettings->id) }}" enctype="multipart/form-data">                                            		
                                    	<input type="hidden" name="id" value="{{ $paysettings->id }}">			
                                      <input type="hidden" name="_method" value="PUT">
          		
                                      {{ csrf_field() }}  

                                      <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Paypal Url</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="paypal_url" class="form-control" name="paypal_url" value="{{ $paysettings->paypal_url }}" placeholder="Paypal Url" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Paypal Email*</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="paypal_email" class="form-control" name="paypal_email" value="{{ $paysettings->paypal_email }}" placeholder="Paypal Email" required>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Max Amount</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="paypal_max_amount" class="form-control" name="paypal_max_amount" value="{{ $paysettings->paypal_max_amount }}" placeholder="Max Amount" required>
                                                                <input type="hidden" name="auth_max_amount" value="{{ $paysettings->auth_max_amount }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Currency Type</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="currency_type" class="form-control" name="currency_type" value="{{ $paysettings->currency_type }}" placeholder="Currency Type" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Quotation Expire (in days)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="quotation_expiry_day" class="form-control" name="quotation_expiry_day" value="{{ $paysettings->quotation_expiry_day }}" placeholder="Quotation Expire (in days)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Minimum Cart Amount for Free shipping (Singapore)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="free_shipping_amount" class="form-control" name="free_shipping_amount" value="{{ $paysettings->free_shipping_amount }}" placeholder="Minimum Cart Amount for Free shipping" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Local Free shipping Message</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="local_free_shipping_msg" class="form-control" name="local_free_shipping_msg" value="{{ $paysettings->local_free_shipping_msg }}" placeholder="Local Free shipping Message" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>International Free shipping Message</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="international_free_shipping_msg" class="form-control" name="international_free_shipping_msg" value="{{ $paysettings->international_free_shipping_msg }}" placeholder="International Free shipping Message" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Overseas Fuel Charges Percentage(%)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="fuelcharge_percentage" class="form-control" name="fuelcharge_percentage" value="{{ $paysettings->fuelcharge_percentage }}" placeholder="Overseas Fuel Charge (%)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Shipping Cost</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="shipping_cost" class="form-control" name="shipping_cost" value="{{ $paysettings->shipping_cost }}" placeholder="Shipping Cost" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Global Discount %</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="discount_percentage" class="form-control" name="discount_percentage" value="{{ $paysettings->discount_percentage }}" placeholder="Global Discount" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Promotion Discount %</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="promo_discount_percentage" class="form-control" name="promo_discount_percentage" value="{{ $paysettings->promo_discount_percentage }}" placeholder="Promotion Discount" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(P) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="P_package_fee" class="form-control" name="P_package_fee" value="{{ $paysettings->P_package_fee }}" placeholder="Packaging Fee(P)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(XXL) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="XXL_package_fee" class="form-control" name="XXL_package_fee" value="{{ $paysettings->XXL_package_fee }}" placeholder="Packaging Fee(XXL)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(XL) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="XL_package_fee" class="form-control" name="XL_package_fee" value="{{ $paysettings->XL_package_fee }}" placeholder="Packaging Fee(XL)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(L) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="L_package_fee" class="form-control" name="L_package_fee" value="{{ $paysettings->L_package_fee }}" placeholder="Packaging Fee(L)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(M) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="M_package_fee" class="form-control" name="M_package_fee" value="{{ $paysettings->M_package_fee }}" placeholder="Packaging Fee(M)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(S) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="S_package_fee" class="form-control" name="S_package_fee" value="{{ $paysettings->S_package_fee }}" placeholder="Packaging Fee(S)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(XS) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="XS_package_fee" class="form-control" name="XS_package_fee" value="{{ $paysettings->XS_package_fee }}" placeholder="Packaging Fee(XS)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Packaging Fee(XXS) $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="XXS_package_fee" class="form-control" name="XXS_package_fee" value="{{ $paysettings->XXS_package_fee }}" placeholder="Packaging Fee(XXS)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Minimum Packaging Fee $</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="min_package_fee" class="form-control" name="min_package_fee" value="{{ $paysettings->min_package_fee }}" placeholder="Minimum Packaging Fee" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1" name="Submit" value="Submit">Save</button>
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