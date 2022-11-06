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
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/international_shipping_methods') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                       <form class="form form-horizontal" name="international_shipping_methods" id="international_shipping_methods" method = "post" action="{{ url('/admin/international_shipping_methods/'.$international_shipping_methods->Id) }}" enctype="multipart/form-data">                                            			
										<input type="hidden" name="id" value="{{ $international_shipping_methods->Id }}">			
										<input type="hidden" name="_method" value="PUT">                                                  
                                            {{ csrf_field() }}
											
											
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Zone <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="Zone" class="form-control" name="Zone" value="{{ $international_shipping_methods->Zone }}" placeholder="Zone" required>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <span>Countries</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span>Selected Country(s)</span>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
															  <select name="SelectedItemId[]" class="form-control" multiple="multiple" id="SelectedItemId">
																		  
															   @foreach($countries as $countrie)
																	 <option value="{{ $countrie->countryid }}" selected="selected">{{ $countrie->countryname }}</option>
															   @endforeach
                        
                    
															</select>
                                                            </div>
															
															<div class="col-md-1">
															<a href="javascript:void(0)" class="pagelink" onclick="add_item()"> <i class="fa fa-forward fa-lg" aria-hidden="true"></i></a>
															<br><br>
															<a href="javascript:void(0)" onclick="remove_item()" class="pagelink"><i class="fa fa-backward fa-lg" aria-hidden="true"></i></a>
															</div>
															@php
																$countrieslist = [];
																$countrieslistarr = $international_shipping_methods->CountriesList;
																if($countrieslistarr) {
																	$countrieslist = @explode(',', $countrieslistarr);
																}
															@endphp
                                                            <div class="col-md-5">
                                                              <select name="CountryIds[]" class="form-control" multiple="multiple" id="ItemId">
																
																@foreach($countries as $countrie)
																	@if(in_array($countrie->countryid, $countrieslist))
																	<option value="{{ $countrie->countryid }}" selected="selected"> {{ $countrie->countryname }}</option>	
																	@endif
																@endforeach															
															  
															</select>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Shipping Amount</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
																<textarea name="ShipCost" class="form-control">{{ $international_shipping_methods->ShipCost }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Shipping Amount more than 30KG</span>
                                                            </div>
                                                            <div class="col-md-2">                                                                
																 <span>30.1 KG To 70 KG</span>
                                                            </div>
															<div class="col-md-2">                                                                
																 <span>70.1 KG To 300 KG</span>
                                                            </div>
															<div class="col-md-4">                                                                
																 <span>300.1 KG To 99,999 KG</span>
                                                            </div>
                                                        </div>
                                                    </div>
														<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Value (Multiply value)</span>
                                                            </div>
															<div class="col-md-2">
                                                                <input type="text" id="PriceRange70" value = "{{ $international_shipping_methods->PriceRange70 }}" class="form-control" name="PriceRange70" placeholder="Price">
                                                            </div>
                                                            <div class="col-md-2">
                                                                 <input type="text" id="PriceRange300" value = "{{ $international_shipping_methods->PriceRange300 }}" class="form-control" name="PriceRange300" placeholder="Price">
                                                            </div>
															<div class="col-md-2">
                                                                 <input type="text" id="PriceRange99999" class="form-control" value = "{{ $international_shipping_methods->PriceRange99999 }}" name="PriceRange99999" placeholder="Price">
                                                            </div>
														</div>
                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Free Shipping Amount</span>
                                                            </div>
                                                            <div class="col-md-8">
																<input type="text" name="FreeShippingCost" class="form-control" id="FreeShippingCost" placeholder="Free Shipping Cost" value="{{ $international_shipping_methods->FreeShippingCost }}">
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
																	<option value="0" @if($international_shipping_methods->Status == 0) selected @endif>In-Active</option>
																	<option value="1" @if($international_shipping_methods->Status == 1) selected @endif>Active</option>
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
<script language="javascript">


function add_item(){
 return !$('#SelectedItemId option:selected').appendTo('#ItemId')
}

function remove_item(){
	return !$('#ItemId option:selected').remove(); 	
	$("#ItemId option").each(function()
	{
    $(this).prop('selected', true);
	});
}
</script>