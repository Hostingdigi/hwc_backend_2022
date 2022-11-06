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
        <div class="content-wrapper" style="width:80%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Advertisement Banner</h2>                            
                        </div>
						@if(in_array('10_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/banner_ads/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
	<form class="form form-horizontal" name="type_frm" id="type_frm" method = "post" action="{{ url('/admin/banner_ads/bulkupdate') }}" enctype="multipart/form-data"> 						
			{{ csrf_field() }}
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
			
                <div class="table-responsive">
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>Id</th>
                                    <th>Image Title</th>  
									<th>Display Order</th> 	                                                                      
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
							@foreach($bannerads as $banner_ads)
                                    <tr>
									<td>
									<input type="checkbox" value="{{ $banner_ads->ban_id }}" name="banneradsid[]"></td>
									<td>{{ $banner_ads->ban_name }}</td>                            
                            		<td><input type = "text" name="display_order{{ $banner_ads->ban_id }}" id="display_order{{ $banner_ads->ban_id }}" class="form-control col-md-3 display_order" value = "{{ $banner_ads->display_order }}">
							</td>	                                                                            
                                	<td>
									@if(in_array('10_Status', $moduleaccess) || $adminrole == 0)
									<a href="javascript:void(0);" onclick="chkupdatestatus({{ $banner_ads->ban_id }}, {{ $banner_ads->ban_status }})">
									@if($banner_ads->ban_status != 0) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif 
									</a> &nbsp;&nbsp;|&nbsp;&nbsp;
									@endif
									
									@if(in_array('10_Edit', $moduleaccess) || $adminrole == 0)
									<a href="{{ url('/admin/banner_ads/'.$banner_ads->ban_id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
									@endif
									@if(in_array('10_Delete', $moduleaccess) || $adminrole == 0)
									<a href="javascript:void(0);" onclick="chkdelete({{ $banner_ads->ban_id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a> 
									@endif
									</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
							
					<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($bannerads->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($bannerads->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $bannerads->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($bannerads->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $bannerads->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($bannerads->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $bannerads->lastPage()) as $i)
										@if($i >= $bannerads->currentPage() - 2 && $i <= $bannerads->currentPage() + 2)
											@if ($i == $bannerads->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $bannerads->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($bannerads->currentPage() < $bannerads->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($bannerads->currentPage() < $bannerads->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $bannerads->appends($_GET)->url($bannerads->lastPage()) }}">{{ $bannerads->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($bannerads->hasMorePages())
										<li><a href="{{ $bannerads->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
									@else
										<li class="disabled"><span>»</span></li>
									@endif
								</ul>
							@endif
						</div>		
						@if(in_array('10_Bulk Action', $moduleaccess) || $adminrole == 0)
						<div class="col-md-3" style="float:right; text-align:right; margin-bottom:20px;">
							<select name="bulk_action" id="bulk_action" class="form-control" onchange="bulk_update_item(this.value)">
									<option value="">Select Bulk Action </option>
									<option value="delete">Delete</option>
									<option value="update_disporder">Update Display Order</option>
							</select>
						</div>
						@endif
					</div>
                </div>
				
				
            </section>
            </div>
			</form>
        </div>
</div>





    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<script type="text/javascript">
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this Page Content ?');
	if(chk) {
		window.location = "{{ url('/admin/banner_ads') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this Static Page ?');
	if(chk) {
		window.location = "{{ url('/admin/banner_ads') }}/"+id+"/"+status+"/updatestatus";
	}
}

function bulk_update_item(val){
	if(val != '') {
	var del_process = 0;
	for(i=0; i < window.document.type_frm.elements.length; i++)
	{	

		if(window.document.type_frm.elements[i].name == "banneradsid[]")
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
}
</script>	

