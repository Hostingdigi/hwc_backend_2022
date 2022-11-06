@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:92%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Brands</h2>                            
                        </div>
						@if(in_array('19_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/brands/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
			
		
		 <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            	<form name="brand_filter_frm" action="{{ url('/admin/brands') }}" method="GET">
					<div class="col-md-6 col-6 col-md-offset-3" style="margin:0 auto;">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Filter Brands</h4>
							</div>
							<div class="card-content">
								<div class="card-body">
									<form class="form form-horizontal">
										<div class="form-body">
											<div class="row">
											<div class="col-12">
													<div class="form-group row">
														<div class="col-md-4">
															<span>Brand</span>
														</div>
														<div class="col-md-3">
														<select class="form-control" name="Nametype">
																<option value="1" @if($Nametype == '1') selected @endif>Equals</option>
																<option value="2" @if($Nametype == '2') selected @endif>Contains</option>
															</select>
														</div>
														<div class="col-md-5">
														<input type="text" id="EnName" class="form-control" name="EnName" placeholder="Brand Name" value="{{ $EnName }}">
														</div>
													</div>
												</div>
												<div class="col-12">
													<div class="form-group row">
														<div class="col-md-4">
															<span>Popular Brands</span>
														</div>
														<div class="col-md-8">
															<select class="form-control" name="PopularBrand">
																<option value="">Show All Type</option>
																<option value="1" @if($PopularBrand == '1') selected @endif>Yes</option>
																<option value="0" @if($PopularBrand == '0') selected @endif>No</option>
															</select>
														</div>
													</div>
												</div>                                                                                                                                                   
												<div class="col-12">
													<div class="form-group row">
														<div class="col-md-4">
															<span>Is Promo Type ?</span>
														</div>
														<div class="col-md-8">
														<select class="form-control" name="dis_type">
																<option value="">Default</option>
																<option value="1" @if($dis_type == '1') selected @endif>Yes</option>
																<option value="0" @if($dis_type == '0') selected @endif>No</option>
															</select>
														</div>															
													</div>
												</div>
												<div class="col-12">
													<div class="form-group row">
														<div class="col-md-4">
															<span>General Discount Excluded</span>
														</div>
														<div class="col-md-8">
														<select class="form-control" name="exclude_global_dis">
																<option value="">Default</option>
																<option value="0" @if($exclude_global_dis == '0') selected @endif>Yes</option>
																<option value="1" @if($exclude_global_dis == '1') selected @endif>No</option>
															</select>
														</div>															
													</div>
												</div>
												<div class="col-md-8 offset-md-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Search</button>
													<button type="reset" id="brandreset" class="btn btn-outline-warning mr-1 mb-1">Clear</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</form>
				
				<form class="form form-horizontal" name="brand_frm" id="brand_frm" method = "post" action="{{ url('/admin/brands/bulkupdate') }}" enctype="multipart/form-data">   						
				{{ csrf_field() }}
                <div class="table-responsive">
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>ID</th>
                                    <th>Brand</th>                                                                        
                                    <th>Promo Type</th>                                                                        
                                    <th>Exclude Global Discount ?</th>
                                    <th>General Member</th>
									<th>Price</th>	
                                    <th>Display Order</th>                                                                        
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($brands) > 0)
                               @foreach($brands as $brand)
                                    <tr>
										<td><input type="checkbox" value="{{ $brand->BrandId }}" name="brandids[]"></td>
										<td>{{ $brand->EnName }}</td>
										<td>@if($brand->dis_type == 1) Yes @else No @endif</td>
										<td>@if($brand->exclude_global_dis == 0) Included @else Excluded @endif</td>
										<td>
										  <select name="type{{ $brand->BrandId }}" class="form-control">
												<option value=""></option>
												<option value="1">Up </option>
												 <option value="2">Discount</option>
										 </select></td>
										 <td>
										<input type="text" name="Price{{ $brand->BrandId }}" class="form-control" value="">
										</td>
										<td>
										<input type = "text" name="DisplayOrder{{ $brand->BrandId }}" id="DisplayOrder{{ $brand->BrandId }}" class="form-control col-md-3 DisplayOrder" value = "{{ $brand->DisplayOrder }}">
								<input type = "hidden" name="display_orderid[]" id="DisplayOrder{{ $brand->BrandId }}" class="form-control col-md-2 display_orderid" value = "{{ $brand->BrandId }}"></td>
										<td> 
										@if(in_array('19_Status', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $brand->BrandId }}, {{ $brand->BrandStatus }})">
											@if($brand->BrandStatus != 0) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif 
										</a> &nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('19_Edit', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/brands/'.$brand->BrandId.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('19_Delete', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkdelete({{ $brand->BrandId }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
										@endif
										
										<br>
										<a href="{{ url('/admin/brands/'.$brand->BrandId.'/groupprice') }}" >Group Price</a>
										
										</td>
                                    </tr>
                                @endforeach
							@else
								<tr><td colspan="8" align="center">No Records Found</td></tr>
							@endif		
                            </tbody>
                            </table>
                </div>
				<div class="row" style="padding:10px 0;">
					<div class="col-md-10">
						@if ($brands->hasPages())
							<ul class="pagination pagination">
								{{-- Previous Page Link --}}
								@if ($brands->onFirstPage())
									<li class="disabled"><span>«</span></li>
								@else
									<li><a href="{{ $brands->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
								@endif

								@if($brands->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $brands->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($brands->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $brands->lastPage()) as $i)
									@if($i >= $brands->currentPage() - 2 && $i <= $brands->currentPage() + 2)
										@if ($i == $brands->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $brands->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($brands->currentPage() < $brands->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($brands->currentPage() < $brands->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $brands->appends($_GET)->url($brands->lastPage()) }}">{{ $brands->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($brands->hasMorePages())
									<li><a href="{{ $brands->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
								@else
									<li class="disabled"><span>»</span></li>
								@endif
							</ul>
						@endif
					</div>
					@if(in_array('19_Bulk Action', $moduleaccess) || $adminrole == 0)
					<div class="col-md-2" style="float:right; text-align:right; margin-bottom:20px;">
						<select name="bulk_action" id="bulk_action" class="form-control" onchange="bulk_update_item(this.value)">
							<option value="">Select </option>
							<option value="delete">Delete</option>
							<option value="assign_promo">Mark as Promo Brand</option>
							<option value="remove_promo">Remove from Promo Brand</option>
							<option value="assign_papular">Mark as Popular Brand</option>
							<option value="remove_papular">Remove from Popular Brand</option>
							<option value="exclude_discount">Exclude Global Discount</option>
							<option value="include_discount">Include Global Discount</option>
							<option value="update_group_price">Update Brand Member Prices </option>
							<option value="update_disporder">Update Display Order</option>
						</select>
					</div>
					@endif
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
	var chk = confirm('Are you sure! you want to delete this brand?');
	if(chk) {
		window.location = "{{ url('/admin/brands') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this brand?');
	if(chk) {
		window.location = "{{ url('/admin/brands') }}/"+id+"/"+status+"/updatestatus";
	}
}

function bulk_update_item(val){
	if(val != '') {
		var del_process = 0;
		for(i=0; i < window.document.brand_frm.elements.length; i++)
		{		
			if(window.document.brand_frm.elements[i].name == "brandids[]")
			{
				if(window.document.brand_frm.elements[i].checked == true)
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
				window.document.brand_frm.submit();
			 }
		}
	}
}
</script>	
<script>
$(document).ready(function() {
	$('#brandreset').click(function() {
		window.location = "{{ url('/admin/brands') }}";
	});
});


</script>
