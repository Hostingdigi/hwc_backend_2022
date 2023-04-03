@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->

<div class="app-content content" style="background-color:#fff;">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper" style="width:90%; margin:0 auto;">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Manage Category</h2>                            
					</div>
					@if(in_array('18_Add', $moduleaccess) || $adminrole == 0)
					<div class="col-4" style="float:right; text-align:right;">
						<div class="dt-buttons btn-group"><a href="{{ url('/admin/category/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
						<i class="feather icon-plus"></i> Add New</span></a> </div>
					</div>
					@endif
				</div>
			</div>                
		</div>
			
		
		<div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
				<form name="category_filter_frm" action="{{ url('/admin/category') }}" method="GET">
				<div class="col-md-6 col-6 col-md-offset-3" style="margin:0 auto;">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Filter Category</h4>
						</div>
						<div class="card-content">
							<div class="card-body">								
								<div class="form-body">
									<div class="row">
										<div class="col-12">
										<div class="form-group row">
												<div class="col-md-4">
													<span>Category</span>
												</div>
												<div class="col-md-8">
												<select name="Types" class="form-control" >	
													<option value="">Select Category</option>
													<option value="0" @if($Types == '0') selected @endif>Top Level</option>
													@if(count($categories) > 0)						

														@foreach($categories as $cate)							

														<option value="{{ $cate->TypeId }}" @if($cate->TypeId == $Types) selected @endif>{{ $cate->EnName }}</option>							

														@php								

														$subcats = [];								

														$subcats = \App\Models\Category::where('ParentLevel','=',$cate->TypeId)->orderBy('EnName', 'ASC')->get();
														@endphp								

														@if(count($subcats) > 0)								

														@foreach($subcats as $subcat)									

														<option value="{{ $subcat->TypeId }}" @if($subcat->TypeId == $Types) selected @endif>&nbsp;&nbsp >> {{ $subcat->EnName }}</option>		@php
																	$subsubcats = [];
																	$subsubcats = \App\Models\Category::where('ParentLevel','=',$subcat->TypeId)->orderBy('EnName', 'ASC')->get();
																	@endphp
																	@if(count($subsubcats) > 0)
																		@foreach($subsubcats as $subsubcat)
																			<option value="{{ $subsubcat->TypeId }}" @if($subsubcat->TypeId == $Types) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp; >>> {{ $subsubcat->EnName }}</option>
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
														<span>Category Name</span>
													</div>
													<div class="col-md-3">
													<select class="form-control" name="Nametype">
															<option value="1" @if($Nametype == '1') selected @endif>Equals</option>
															<option value="2" @if($Nametype == '2') selected @endif>Contains</option>
														</select>
													</div>
													<div class="col-md-5">
													<input type="text" id="EnName" class="form-control" name="EnName" placeholder="Category Name" value="{{ $EnName }}">
													</div>
												</div>
											</div>
									
										  <div class="col-12">
											<div class="form-group row">
												<div class="col-md-4">
													<span>Is Promo Type?</span>
												</div>
												<div class="col-md-8">
													<select class="form-control" name="dis_type">
														<option value="" @if($dis_type == '') selected @endif>Default</option>
														<option value="1" @if($dis_type == '1') selected @endif>Yes</option>
														<option value="0" @if($dis_type == '0') selected @endif>No</option>
													</select>
												</div>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group row">
												<div class="col-md-4">
													<span>Is Offer Category?</span>
												</div>
												<div class="col-md-8">
													<select class="form-control" name="offerCategory">
														<option value="" @if($offerCategory == '') selected @endif>Default</option>
														<option value="1" @if($offerCategory == '1') selected @endif>Yes</option>
														<option value="0" @if($offerCategory == '0') selected @endif>No</option>
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
													<select class="form-control" name="TypeStatus">
														<option value="" @if($TypeStatus == '') selected @endif>All Category</option>
														<option value="1" @if($TypeStatus == '1') selected @endif>Active</option>
														<option value="0" @if($TypeStatus == '0') selected @endif>In-Active</option>
													</select>
												</div>
											</div>
										</div>
										 <div class="col-md-8 offset-md-4">
											<button type="submit" class="btn btn-primary mr-1 mb-1">Search</button>
											<button type="reset" id="categoryreset" class="btn btn-outline-warning mr-1 mb-1">Clear</button>
										</div>
									
									</div>									
								</div>
							</div>
						</div>
					</div>
				</div>
				</form>
				<form class="form form-horizontal" name="type_frm" id="type_frm" method = "post" action="{{ url('/admin/category/bulkupdate') }}" enctype="multipart/form-data"> 						
				{{ csrf_field() }}	
                <div class="table-responsive">				
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>ID</th>
                                    <th>Types</th>
                                    <th>Level</th>                                                                        
                                    <th>Promo Type?</th>
									<th>Display Order</th>									
									<th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($categories) > 0)
								@foreach($categories as $category)
                                    <tr>
									<td>
									<input type="checkbox" value="{{ $category->TypeId }}" name="typeids[]"></td>
                                        <td>{{ $category->EnName }}</td>
                                        <td>
										@if($category->ParentLevel == 0) 
											Top Level 
										@else
											@php
												$parentname = '';
												$parent = \App\Models\Category::where('TypeId','=',$category->ParentLevel)->first();
											@endphp
											@if($parent)
												@php $parentname = $parent->EnName; @endphp
											@endif
											{{ $parentname }}
										@endif
										</td>
										
										<td>@if($category->dis_type == 1) Yes @else No @endif</td>
										 <td><input type = "text" name="DisplayOrder{{ $category->TypeId }}" id="DisplayOrder{{ $category->TypeId }}" class="form-control col-md-3 DisplayOrder" value = "{{ $category->DisplayOrder }}">
											<input type = "hidden" name="display_orderid[]" id="DisplayOrder{{ $category->TypeId }}" class="form-control col-md-2 display_orderid" value = "{{ $category->TypeId }}"></td>
										<td>
										@if(in_array('18_Status', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $category->TypeId }}, {{ $category->TypeStatus }})">@if($category->TypeStatus == 1) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('18_Edit', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/category/'.$category->TypeId.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('18_Delete', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkdelete({{ $category->TypeId }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
										@endif
									</td>
                                    </tr>
                                @endforeach
							@else
								<tr><td colspan="6" align="center">No Records Found</td></tr>
							@endif	
                            </tbody>
                            </table>
							
							<div class="row" style="padding:10px 0;">
								<div class="col-md-10">
									@if ($categories->hasPages())
										<ul class="pagination pagination">
											{{-- Previous Page Link --}}
											@if ($categories->onFirstPage())
												<li class="disabled"><span>«</span></li>
											@else
												<li><a href="{{ $categories->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
											@endif

											@if($categories->currentPage() > 3)
												<li class="hidden-xs"><a href="{{ $categories->appends($_GET)->url(1) }}">1</a></li>
											@endif
											@if($categories->currentPage() > 4)
												<li><span>...</span></li>
											@endif
											@foreach(range(1, $categories->lastPage()) as $i)
												@if($i >= $categories->currentPage() - 2 && $i <= $categories->currentPage() + 2)
													@if ($i == $categories->currentPage())
														<li class="active"><span>{{ $i }}</span></li>
													@else
														<li><a href="{{ $categories->appends($_GET)->url($i) }}">{{ $i }}</a></li>
													@endif
												@endif
											@endforeach
											@if($categories->currentPage() < $categories->lastPage() - 3)
												<li><span>...</span></li>
											@endif
											@if($categories->currentPage() < $categories->lastPage() - 2)
												<li class="hidden-xs"><a href="{{ $categories->appends($_GET)->url($categories->lastPage()) }}">{{ $categories->lastPage() }}</a></li>
											@endif

											{{-- Next Page Link --}}
											@if ($categories->hasMorePages())
												<li><a href="{{ $categories->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
											@else
												<li class="disabled"><span>»</span></li>
											@endif
										</ul>
									@endif
								</div>
								@if(in_array('18_Bulk Action', $moduleaccess) || $adminrole == 0)
								<div class="col-md-2" style="float:right; text-align:right;">
								<select name="bulk_action" id="bulk_action" class="form-control" onchange="bulk_update_item(this.value)">
									<option value="">Select </option>
									<option value="delete">Delete</option>
									<option value="status_active">Mark as Active</option>
									<option value="status_inactive">Mark as InActive</option>
									<option value="assign_promo">Mark as Promo Prod</option>
									<option value="remove_promo">Remove from Promo Prod</option>
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
	var chk = confirm('Are you sure! you want to delete this category?');
	if(chk) {
		window.location = "{{ url('/admin/category') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this category?');
	if(chk) {
		window.location = "{{ url('/admin/category') }}/"+id+"/"+status+"/updatestatus";
	}
}

function bulk_update_item(val){
	var del_process = 0;
	for(i=0; i < window.document.type_frm.elements.length; i++)
	{		
		if(window.document.type_frm.elements[i].name == "typeids[]")
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
</script>	
<script>
$(document).ready(function() {
	$('#categoryreset').click(function() {
		window.location = "{{ url('/admin/category') }}";
	});
});

</script>
