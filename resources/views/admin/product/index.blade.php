@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:100%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Products</h2>                            
                        </div>
						@if(in_array('20_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/products/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
		 
		 <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
				<form name="product_filter_frm" action="{{ url('/admin/products') }}" method="GET">
				<div class="col-md-6 col-6 col-md-offset-3" style="margin:0 auto;">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Filter Products</h4>
						</div>
						<div class="card-content">
							<div class="card-body">
								<form class="form form-horizontal">
									<div class="form-body">
										<div class="row">
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Status</span>
													</div>
													<div class="col-md-8">
														<select class="form-control" name="status">
															<option value="" @if($status == '') selected @endif>Select All Products</option>
															<option value="1" @if($status == '1') selected @endif>Active</option>
															<option value="0" @if($status == '0') selected @endif>In-Active</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Category</span>
													</div>
													<div class="col-md-8">
													<select name="Types" class="form-control">	
														<option value="">Select Category</option>
														@if(count($categories) > 0)
															@foreach($categories as $cate)							

															<option value="{{ $cate->TypeId }}" @if($Types == $cate->TypeId) selected @endif>{{ $cate->EnName }}</option>							

															@php								

															$subcats = [];								

															$subcats = \App\Models\Category::where('ParentLevel','=',$cate->TypeId)->orderBy('EnName', 'ASC')->get();
															@endphp								

															@if(count($subcats) > 0)								

															@foreach($subcats as $subcat)									

															<option value="{{ $subcat->TypeId }}" @if($Types == $subcat->TypeId) selected @endif>&nbsp;&nbsp >> {{ $subcat->EnName }}</option>
															@php															
																$subsubcats = [];
																$subsubcats = \App\Models\Category::where('ParentLevel','=',$subcat->TypeId)->orderBy('EnName', 'ASC')->get();
																@endphp
																@if(count($subsubcats) > 0)
																	@foreach($subsubcats as $subsubcat)
																		<option value="{{ $subsubcat->TypeId }}" @if($Types == $subsubcat->TypeId) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp; >>> {{ $subsubcat->EnName }}</option>
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
														<span>Brands</span>
													</div>
													<div class="col-md-8">
													<select name="Brand" class="form-control">
														<option value="0">Select Brand</option>            		
															@if(count($brands) > 0)
																@foreach($brands as $brand)							
																	<option value="{{ $brand->BrandId }}" @if($brand->BrandId == $Brand) selected @endif>{{ $brand->EnName }}</option>
																@endforeach
															@endif			    
														</select>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Product Name</span>
													</div>
													<div class="col-md-3">
													<select class="form-control" name="Nametype">
															<option value="1" @if($Nametype == '1') selected @endif>Equals</option>
															<option value="2" @if($Nametype == '2') selected @endif>Contains</option>
														</select>
													</div>
													<div class="col-md-5">
													<input type="text" id="EnName" class="form-control" name="EnName" placeholder="Product Name" value="{{ $EnName }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Quantity</span>
													</div>
													<div class="col-md-3">
													<select class="form-control" name="Qtytype">
															<option value="1" @if($Qtytype == '1') selected @endif>Equals To</option>
															<option value="2" @if($Qtytype == '2') selected @endif>Less than</option>
														</select>
													</div>
													<div class="col-md-5">
													<input type="text" id="Qty" class="form-control" name="Qty" placeholder="Quantity" value="{{ $Qty }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Product Code</span>
													</div>
													<div class="col-md-8">
														<input type="text" id="productcode" class="form-control" name="productcode" placeholder="Product Code" value="{{ $productcode }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Barcode</span>
													</div>
													<div class="col-md-8">
														<input type="text" id="barcode" class="form-control" name="barcode" placeholder="Product Barcode" value="{{ $barcode }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Is Promo Product?</span>
													</div>
													<div class="col-md-8">
													<select class="form-control" name="IsPromotion">
															<option value="">Default</option>
															<option value="1" @if($IsPromotion == '1') selected @endif>Yes</option>
															<option value="0" @if($IsPromotion == '0') selected @endif>No</option>
														</select>
													</div>															
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-4">
														<span>Sort By</span>
													</div>
													<div class="col-md-8">
													<select class="form-control" name="sortcolumn">
															<option value="">Default</option>
															<option value="1" @if($sortcolumn == '1') selected @endif>Brand</option>
															<option value="2" @if($sortcolumn == '2') selected @endif>Category</option>
															<option value="3" @if($sortcolumn == '3') selected @endif>Name Ascending</option>
															<option value="4" @if($sortcolumn == '4') selected @endif>Name Descending</option>
														</select>
													</div>															
												</div>
											</div>
											<div class="col-md-8 offset-md-4">
												<button type="submit" class="btn btn-primary mr-1 mb-1">Search</button>
												<button type="reset" id="reset" class="btn btn-outline-warning mr-1 mb-1">Clear</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				</form>
				<input type="checkbox" name="selectall" id="selectall" onClick="check_uncheck_checkbox(this.checked);" style="margin-left:15px; margin-top:25px;">&nbsp;Select All
				@if(in_array('20_Export CSV', $moduleaccess) || $adminrole == 0)
				<a href="{{ url('/admin/exportproducts?status='.$status.'&Types='.$Types.'&Brand='.$Brand.'&Nametype='.$Nametype.'&EnName='.$EnName.'&Qtytype='.$Qtytype.'&Qty='.$Qty.'&productcode='.$productcode.'&IsPromotion='.$IsPromotion.'&sortcolumn='.$sortcolumn) }}" id="export" class="btn btn-primary mr-1 mb-1 float-right" target="_blank">Export CSV</a>
				@endif
				<form class="form form-horizontal" name="type_frm" id="type_frm" method = "post" action="{{ url('/admin/products/bulkupdate') }}" enctype="multipart/form-data"> 
                <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors table-bordered" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>ID</th>
                                    <th>Products</th>                                                                        
                                    <th>Type(s)</th>                                                                        
                                    <th>Brands(s)</th>                                                                        
                                    <th>Promo Item ?</th>                                                                        
                                    <th>Price($)</th>                                                                        
                                    <th>Quantity</th>                                                                        
                                    <th>Weight in KG</th>                                                                        
                                    <th>Box Size</th>                                                                        
                                    <th>Cust Qty / Day</th>                                                                        
                                    <th>Display Order</th>                                                                        
                                    <th >Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($products) > 0)
                               @foreach($products as $product)
                                    <tr>
                                        <td><input type="checkbox" value="{{ $product->Id }}" name="productids[]"></td>
										<td>{{ $product->EnName }} 
										
										@if($product->barcode!='')
										<br>
										<a style="font-size:12px;font-style: italic;text-decoration:underline;" href="#" alt="{{$product->barcode}}" title="{{$product->barcode}}">Product Code</a>
										@endif
										<!--<span style="font-size:12px;font-style: italic;">{{($product->barcode!='')?"Barcode :".$product->barcode:'' }}</span> --></td>
										<td>							
												@php
													$categoryname = '';
													$category = \App\Models\Category::where('TypeId','=',$product->Types)->first();
												@endphp
												@if($category)
													@php $categoryname = $category->EnName; @endphp
												@endif
												{{ $categoryname }}							
										</td>
										<td>							
												@php
													$brandname = '';
													$brand = \App\Models\Brand::where('BrandId','=',$product->Brand)->first();
												@endphp
												@if($brand)
													@php $brandname = $brand->EnName; @endphp
												@endif
												{{ $brandname }}							
										</td>
										<td>@if($product->IsPromotion == 1) Yes @else No @endif</td>
										<td><input type="text" name="Price{{ $product->Id }}" value="{{ $product->Price }}" class="form-control"></td>
										<td><input type="text" name="Qty{{ $product->Id }}" value="{{ $product->Quantity }}" class="form-control" style="width:55px;"></td>
										<td align="center"><input type="text" name="Weight{{ $product->Id }}" value="{{ $product->Weight }}" class="form-control" style="width:55px;"> </td>
										<td align="center"><input type="text" name="ShippingBox{{ $product->Id }}" value="{{ $product->ShippingBox }}" class="form-control" style="width:55px;"></td>
										<td align="center"><input type="text" name="cust_qty{{ $product->Id }}" value="{{ $product->cust_qty_per_day }}" class="form-control" style="width:55px;"></td> 
										<td align="center"><input type="text" name="dorder{{ $product->Id }}" value="{{ $product->DisplayOrder }}" class="form-control" style="width:55px;"></td>
										
										<td align="center"  width="20%">
										@if(in_array('20_Status', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $product->Id }}, {{ $product->ProdStatus }})">@if($product->ProdStatus == 1)<i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else
										<i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;
										@endif
										
										@if(in_array('20_Edit', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/products/'.$product->Id.'/edit') }}">
										<i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;
										@endif
										
										@if(in_array('20_Delete', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkdelete({{ $product->Id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>&nbsp;|&nbsp;
										@endif
										
										@if(in_array('20_Quantity Update', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/products/'.$product->Id.'/quantity/') }}" >Quantity</a>&nbsp;|&nbsp;
										@endif
										
										@if(in_array('20_Group Price', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/products/'.$product->Id.'/groupprice/') }}" >Group Price</a>&nbsp;|&nbsp;
										@endif
										@if(in_array('20_Product Options', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/products/'.$product->Id.'/productoptions/') }}" >Options</a>&nbsp;|&nbsp;
										@endif
										
										@if(in_array('20_Images', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/products/'.$product->Id.'/gallery/') }}" >Images</a>
										@endif
									</td>
                                    </tr>
                                @endforeach
							@else
								<tr><td colspan="12" align="center">No Records Found</td></tr>
							@endif
                            </tbody>
                            </table>
							<div class="row" style="padding:10px 0;">
								<div class="col-md-10">
									@if ($products->hasPages())
										<ul class="pagination pagination">
											{{-- Previous Page Link --}}
											@if ($products->onFirstPage())
												<li class="disabled"><span>«</span></li>
											@else
												<li><a href="{{ $products->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
											@endif

											@if($products->currentPage() > 3)
												<li class="hidden-xs"><a href="{{ $products->appends($_GET)->url(1) }}">1</a></li>
											@endif
											@if($products->currentPage() > 4)
												<li><span>...</span></li>
											@endif
											@foreach(range(1, $products->lastPage()) as $i)
												@if($i >= $products->currentPage() - 2 && $i <= $products->currentPage() + 2)
													@if ($i == $products->currentPage())
														<li class="active"><span>{{ $i }}</span></li>
													@else
														<li><a href="{{ $products->appends($_GET)->url($i) }}">{{ $i }}</a></li>
													@endif
												@endif
											@endforeach
											@if($products->currentPage() < $products->lastPage() - 3)
												<li><span>...</span></li>
											@endif
											@if($products->currentPage() < $products->lastPage() - 2)
												<li class="hidden-xs"><a href="{{ $products->appends($_GET)->url($products->lastPage()) }}">{{ $products->lastPage() }}</a></li>
											@endif

											{{-- Next Page Link --}}
											@if ($products->hasMorePages())
												<li><a href="{{ $products->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
											@else
												<li class="disabled"><span>»</span></li>
											@endif
										</ul>
									@endif
								</div>
								
								@if(in_array('20_Bulk Action', $moduleaccess) || $adminrole == 0)
								<div class="col-md-2" style="float:right; text-align:right;">
									<select name="bulk_action" id="bulk_action" class="form-control" onchange="bulk_update_item(this.value)">
										<option value="">Select </option>
										<option value="delete">Delete</option>
										<option value="status_active">Mark as Active</option>
										<option value="status_inactive">Mark as InActive</option>
										<option value="assign_promo">Mark as Promo Prod</option>
										<option value="remove_promo">Remove from Promo Prod</option>
										<option value="update_price">Update Price</option>
										<option value="update_weight">Update Weight</option>
										<option value="update_qty">Update Quantity</option>
										<option value="update_boxsize">Update Box Size</option>
										<option value="update_custqty">Update Customer Purchase Quantity</option>
										<option value="update_disporder">Update Display Order</option>
									</select>
								</div>
								@endif
							</div>	
								

							
                </div>				
				  
			</form>

            </section>
            </div>
			
        </div>
</div>





    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div> 


@include('admin.includes.footer')

<script type="text/javascript">
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this product?');
	if(chk) {
		window.location = "{{ url('/admin/products') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this product?');
	if(chk) {
		window.location = "{{ url('/admin/products') }}/"+id+"/"+status+"/updatestatus";
	}
}

function bulk_update_item(val){
	var del_process = 0;
	for(i=0; i < window.document.type_frm.elements.length; i++)
	{		
		if(window.document.type_frm.elements[i].name == "productids[]")
		{
			if(window.document.type_frm.elements[i].checked == true)
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
		 	window.document.type_frm.submit();
		 }
	}
}

function check_uncheck_checkbox(isChecked) {
	if(isChecked) {
		$('input[name="productids[]"]').each(function() { 
			this.checked = true; 
		});
	} else {
		$('input[name="productids[]"]').each(function() {
			this.checked = false;
		});
	}
}
</script>	
<script>
$(document).ready(function() {
	$('#reset').click(function() {
		window.location = "{{ url('/admin/clearproductsearch') }}";
	});
});

</script>
