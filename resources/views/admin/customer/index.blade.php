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
        <div class="content-wrapper" style="width:85%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Customer</h2>                            
                        </div>
						@if(in_array('16_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/customer/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
			<div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
				<form name="customer_filter_frm" action="{{ url('/admin/customer') }}" method="GET">
				<div class="col-md-8 col-8 col-md-offset-4" style="margin:0 auto;">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Filter Customer</h4>
						</div>
						<div class="card-content">
							<div class="card-body">
								<form class="form form-horizontal">
									<div class="form-body">
										<div class="row">
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-3">
														<span>Status</span>
													</div>
													<div class="col-md-9">
														<select class="form-control" name="status">
															<option value="" @if($status == '') selected @endif>Select All Customer</option>
															<option value="1" @if($status == '1') selected @endif>Active</option>
															<option value="0" @if($status == '0') selected @endif>De - Active</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-3">
														<span>Group:</span>
													</div>
													<div class="col-md-9">
													<select class="form-control" name="cust_type">
														<option value="0">Select Group</option>
														@if($groups)
															@foreach($groups as $group)
															<option value="{{ $group->Id }}" @if($group->Id == $cust_type) selected @endif>{{ $group->GroupTitle }}</option>
															@endforeach
														@endif		
													</select>
													</div>															
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-3">
														<span>Name</span>
													</div>
													<div class="col-md-3">
													<select class="form-control" name="filter_column">
															<option value="cust_firstname" @if($filter_column == 'cust_firstname') selected @endif>First Name</option>
															<option value="cust_lastname" @if($filter_column == 'cust_lastname') selected @endif>Last Name</option>
															<option value="cust_email" @if($filter_column == 'cust_email') selected @endif>Email</option>
															<option value="cust_phone" @if($filter_column == 'cust_phone') selected @endif>Phone</option>
													</select>
													</div>
													<div class="col-md-2">
													<select class="form-control" name="Nametype">							
															<option value="1" @if($Nametype == '1') selected @endif>Equals</option>
															<option value="2" @if($Nametype == '2') selected @endif>Contains</option>	
													</select>
													</div>
													<div class="col-md-4">
													<input type="text" id="filter_srch_val" class="form-control" name="filter_srch_val" placeholder="Value" value="{{ $filter_srch_val }}">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group row">
													<div class="col-md-3">
														<span>Sort By</span>
													</div>
													<div class="col-md-9">
													<select class="form-control" name="sort_column">
														<option value="0" @if($sort_column == '0') selected @endif>Default</option>
														<option value="1" @if($sort_column == '1') selected @endif>First Name Ascending</option>
														<option value="2" @if($sort_column == '2') selected @endif>First Name Descending</option>
														<option value="3" @if($sort_column == '3') selected @endif>Last Name Ascending</option>
														<option value="4" @if($sort_column == '4') selected @endif>Last Name Descending</option>
													</select>
													</div>
												</div>
											</div>
											<div class="col-md-8 offset-md-4">
												<button type="submit" class="btn btn-primary mr-1 mb-1">Search</button>
												<button type="reset" id="resetcustomer" class="btn btn-outline-warning mr-1 mb-1">Clear</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				</form>
                <div class="table-responsive">
					<table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
						<thead>
							<tr style="background:#CCC;">			
								<th>#</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th> 
								<th>Phone Number</th>
								<th>Group</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						@if(count($customers) > 0)
							@foreach($customers as $customer)
								<tr>
								<td>{{ $customer->cust_id }}</td>
								<td>{{ $customer->cust_firstname }}</td>
								<td>{{ $customer->cust_lastname }}</td>
								<td>{{ $customer->cust_email }}</td>
								<td>{{ $customer->cust_phone }}</td>														
								<td>
									@php
										$groupname = 'General Members';
										$group = \App\Models\CustomerGroup::where('Id', '=', $customer->cust_type)->select('GroupTitle')->first();
										if($group) {
											$groupname = $group->GroupTitle;
										}
									@endphp
									{{ $groupname }}
								</td>
								<td>
								@if(in_array('16_Status', $moduleaccess) || $adminrole == 0)
								<a href="javascript:void(0);" onclick="chkupdatestatus({{ $customer->cust_id }},{{ $customer->cust_status }})">
								@if($customer->cust_status == 0) <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @endif </a> 
								&nbsp;&nbsp;| &nbsp;&nbsp; 
								@endif
								
								@if(in_array('16_Edit', $moduleaccess) || $adminrole == 0)
								<a href="{{ url('/admin/customer/'.$customer->cust_id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp; | &nbsp;&nbsp;
								@endif
								@if(in_array('16_Delete', $moduleaccess) || $adminrole == 0)
								<a href="javascript:void(0);" onclick="chkdelete({{ $customer->cust_id }})">
								<i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
								@endif
								</td>
								</tr>
							@endforeach
						@else
							<tr><td colspan="7" align="center">No Records Found</td></tr>
						@endif	
						</tbody>
					</table>
                </div>

				<div class="row" style="padding:10px 0;">
					<div class="col-md-10">
						@if ($customers->hasPages())
							<ul class="pagination pagination">
								{{-- Previous Page Link --}}
								@if ($customers->onFirstPage())
									<li class="disabled"><span>«</span></li>
								@else
									<li><a href="{{ $customers->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
								@endif

								@if($customers->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $customers->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($customers->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $customers->lastPage()) as $i)
									@if($i >= $customers->currentPage() - 2 && $i <= $customers->currentPage() + 2)
										@if ($i == $customers->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $customers->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($customers->currentPage() < $customers->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($customers->currentPage() < $customers->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $customers->appends($_GET)->url($customers->lastPage()) }}">{{ $customers->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($customers->hasMorePages())
									<li><a href="{{ $customers->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
								@else
									<li class="disabled"><span>»</span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
            </section>
            </div>
        </div>
</div>
<div class="sidenav-overlay"></div>
<div class="drag-target"></div>   
@include('admin.includes.footer')

<script type="text/javascript">
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this customer?');
	if(chk) {
		window.location = "{{ url('/admin/customer') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this customer?');
	if(chk) {
		window.location = "{{ url('/admin/customer') }}/"+id+"/"+status+"/updatestatus";
	}
}
</script>	
<script>
$(document).ready(function() {
	$('#resetcustomer').click(function() {
		window.location = "{{ url('/admin/customer') }}";
	});
});

</script>
