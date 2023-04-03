@include('admin.includes.header')





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



        <div class="content-wrapper" style="width:70%; margin:0 auto;"> 

       <div class="content-header row">



                <div class="content-header-left col-md-12 col-12 mb-2">



                    <div class="row breadcrumbs-top">



                        <div class="col-8">



                            <h2 class="content-header-title float-left mb-0">Manage Product Options</h2>                            



                        </div>



						<div class="col-4" style="float:right; text-align:right;">



							<div class="dt-buttons btn-group"><a href="{{ url('/admin/products') }}" class="btn " tabindex="0" aria-controls="Products">



              <span><i class="feather icon-arrow-left"></i> Back</span></a> </div>



						</div>



                    </div>



                </div>                



            </div>

			  <section id="basic-horizontal-layouts">

                    <div class="row match-height">

                        <div class="col-md-12 col-12">

                            <div class="card">                               

                                <div class="card-content">

                                    <div class="card-body">

										<form class="form form-horizontal" name="products_frm" id="products_frm" method = "post" @if($editoption) action="{{ url('/admin/products/updateoptions') }}" @else action="{{ url('/admin/products/addoptions') }}" @endif enctype="multipart/form-data">			

										<input type="hidden" name="id" value="{{ $product->Id }}">			           
										<input type="hidden" name="editid" value="@if($editoption){{ $editoption->Id }}@endif">

										{{ csrf_field() }}	

										<div class="form-body">

                                                <div class="row">
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">
                                                                <span>Product Name </span>
                                                            </div>
                                                            <div class="col-md-8">

                                                                {{ $product->EnName }}

                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @if(($editoption && $adminrole ==5)|| $adminrole !=5)
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Option Title</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <input type="text" name="Title" required {{$field_display}} value="@if($editoption){{ $editoption->Title }}@endif" class="form-control"> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Barcode<br>(Use comma separater for multiple values)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <textarea name="barcode" class="form-control" >@if($editoption){{ $editoption->barcode }}@endif</textarea>
                                                              
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if($adminrole !=5)
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <input type="text" name="Price" required value="@if($editoption){{ $editoption->Price }}@endif" class="form-control"> 
                                                            </div>
                                                        </div>
                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Quantity</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <input type="text" name="Quantity" value="@if($editoption){{ $editoption->Quantity }}@endif" class="form-control"> 
                                                            </div>
                                                        </div>
                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Customer Daily Purchase Limit</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <input type="text" name="cust_qty_per_day" value="@if($editoption){{ $editoption->cust_qty_per_day }}@endif" class="form-control"> 
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

																	   <option value="XXS" @if($editoption) @if($editoption->ShippingBox == 'XXS') selected @endif @endif>XXS (L33.7cm X W18.2cm X H10cm)</option>			

																		<option value="XS" @if($editoption) @if($editoption->ShippingBox == 'XS') selected @endif @endif>XS (L33.6cm X W32cm X H5.2cm)</option>			

																		<option value="S" @if($editoption) @if($editoption->ShippingBox == 'S') selected @endif @endif>S (L33.7cm X W32.2cm X H18cm)</option>				

																		<option value="M" @if($editoption) @if($editoption->ShippingBox == 'M') selected @endif @endif>M (L33.7cm X W32.2cm X H34.5cm)</option>					

																		<option value="L" @if($editoption) @if($editoption->ShippingBox == 'L') selected @endif @endif>L (L41.7cm X W35.9cm X H36.9cm)</option>				

																		<option value="XL" @if($editoption) @if($editoption->ShippingBox == 'XL') selected @endif @endif>XL (L48.1cm X W40.4cm X H38.9cm) </option>				

																		<option value="XXL" @if($editoption) @if($editoption->ShippingBox == 'XXL') selected @endif @endif>XXL (L54.1cm X W44.4cm X H40.9cm)</option>				

																		<option value="P" @if($editoption) @if($editoption->ShippingBox == 'P') selected @endif @endif>P (Pallet Size)</option>               

																</select>

																	

                                                            </div>

                                                        </div>

                                                    </div>

													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Weight (in Kg) (ex: 0.5)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <input type="text" name="Weight" value="@if($editoption){{ $editoption->Weight }}@endif" class="form-control"> 
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
																<option value="1" @if($editoption) @if($editoption->Status == '1') selected @endif @endif>Active</option>
																<option value="0" @if($editoption) @if($editoption->Status == '0') selected @endif @endif>In-Active</option>																  
															</select>
															</div>
                                                        </div>
                                                    </div>
													
                                                    @endif
                                                    @if($editoption || $adminrole !=5)
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
                                                            <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
                                                        </div>
                                                    @endif
                                                   
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

				<div class="table-responsive">
					<form class="form form-horizontal" name="options_frm" id="options_frm" method = "post" action="{{ url('/admin/products/options/bulkoptionupdate') }}" enctype="multipart/form-data">
						<input type="hidden" name="prodid" value="{{ $product->Id }}">
                        <table class="table table-striped dataex-html5-selectors" id="subscriber">

                            <thead>

                                <tr>
								<th></th>
								<th>Title</th>

                                <th>Barcode</th>

								<th>Price</th>

								<th>Quantity</th>
								
								<th>Cust Qty/Day</th>
								
								<th>Box Size</th>

								<th>Actions</th>

								  </tr>

                            </thead>

                            <tbody>						

						 	 @foreach($prodoptions as $option)						

								<tr>														
									<td><input type="checkbox" value="{{ $option->Id }}" name="optionids[]"></td>
									<td>{{ $option->Title }}</td>	
                                    <td>{{ $option->barcode }}</td>														
									<td><input type="text" name="Price{{ $option->Id }}" value="{{ $option->Price }}" class="form-control" style="width:55px;"></td>
									<td><input type="text" name="Quantity{{ $option->Id }}" value="{{ $option->Quantity }}" class="form-control" style="width:55px;"></td>
									<td><input type="text" name="custqty{{ $option->Id }}" value="{{ $option->cust_qty_per_day }}" class="form-control" style="width:55px;"></td>
									<td><input type="text" name="ShippingBox{{ $option->Id }}" value="{{ $option->ShippingBox }}" class="form-control" style="width:55px;"></td>
									<td>
                                    
										<a href="{{ url('/admin/products/options/'.$option->Id.'/'.$option->Prod.'/editoptions') }}">
										<i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>
                                        @if($adminrole !=5)
                                        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="chkupdatestatus({{ $option->Id }}, {{ $option->Status }})">@if($option->Status == 1)<i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else
										<i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> 
                                        &nbsp;&nbsp;<a href="javascript:void(0);" onclick="chkdelete({{ $option->Id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
                                        @endif
                                        </td>
								</tr>						

							@endforeach				

						  </tbody>				

						  </table>		

						  </section>
                          @if($adminrole !=5)
						  <div class="col-md-2" style="float:right; text-align:right; margin-bottom:20px;">
							<select name="bulk_action" id="bulk_action" class="form-control" onchange="bulk_update_item(this.value)">
									<option value="">Select </option>
									<option value="delete">Delete</option>									
									<option value="update_price">Update Price</option>									
									<option value="update_qty">Update Quantity</option>
									<option value="update_boxsize">Update Box Size</option>
									<option value="update_custqty">Update Customer Purchase Quantity</option>
									
							</select>
							</div>	
                            @endif
						</form>
            </div>

        </div>

    </div>

    <div class="sidenav-overlay"></div>

    <div class="drag-target"></div>   

@include('admin.includes.footer')</div>		


<script type="text/javascript">

function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this product option?');
	if(chk) {
		window.location = "{{ url('/admin/products/options') }}/"+id+"/destroyoption";	
	}
}

function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this product option?');
	if(chk) {
		window.location = "{{ url('/admin/products/options') }}/"+id+"/"+status+"/updateoptionstatus";	
	}
}
function bulk_update_item(val){
	if(val != '') {
		var del_process = 0;
		for(i=0; i < window.document.options_frm.elements.length; i++)
		{		
			if(window.document.options_frm.elements[i].name == "optionids[]")
			{
				if(window.document.options_frm.elements[i].checked == true)
				{
					del_process = 1;
					break;
				}
			}
		}
		
		if(del_process == 0)
		{
			alert("Select records to continue !!");
		}
		else
		{
			 doyou = confirm("Do you want to continue? (OK = Yes   Cancel = No)");
			 if (doyou == true)
			 {		  	
				window.document.options_frm.submit();
			 }
		}
	}
}


</script>