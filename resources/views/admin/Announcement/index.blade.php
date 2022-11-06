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
                            <h2 class="content-header-title float-left mb-0">Manage Announcement</h2>                            
                        </div>
						@if(in_array('11_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/announcement/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>
                            <i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th>Select</th>    
                                    
									<th>Message</th>
									<th>Display Order</th>									
									<th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               	@foreach($announcements as $announcement)
                                    <tr>
											<td><input type="checkbox" name="ordnum[]" value="1" onclick="check_request_quote_selection(this,'');"></td>                                        	
											
											<td>{!! $announcement->message !!}</td>
											<td><input type="text" name="dorder{{ $announcement->id }}" value="{{ $announcement->display_order }}" class="form-control" style="width:55px;"></td>
                                            <td>
												@if(in_array('11_Status', $moduleaccess) || $adminrole == 0)
												<a href="javascript:void(0);" onclick="chkupdatestatus({{ $announcement->id }}, {{ $announcement->status }})">
												@if($announcement->status == 1)<i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else
												<i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a>
												&nbsp;&nbsp;|&nbsp;&nbsp;
												@endif
												@if(in_array('11_Edit', $moduleaccess) || $adminrole == 0)
                                                <a href="{{ url('/admin/announcement/'.$announcement->id.'/edit') }}">
												<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                                                </a>
                                                &nbsp;&nbsp;|&nbsp;&nbsp;
												@endif
												@if(in_array('11_Delete', $moduleaccess) || $adminrole == 0)
                                                <a href="javascript:void(0);" onclick="chkdelete({{ $announcement->id }})">
                                                    <i class="fa fa-trash fa-lg" aria-hidden="true"></i>
                                                </a>
												@endif
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
					<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($announcements->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($announcements->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $announcements->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($announcements->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $announcements->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($announcements->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $announcements->lastPage()) as $i)
										@if($i >= $announcements->currentPage() - 2 && $i <= $announcements->currentPage() + 2)
											@if ($i == $announcements->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $announcements->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($announcements->currentPage() < $announcements->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($announcements->currentPage() < $announcements->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $announcements->appends($_GET)->url($announcements->lastPage()) }}">{{ $announcements->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($announcements->hasMorePages())
										<li><a href="{{ $announcements->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
									@else
										<li class="disabled"><span>»</span></li>
									@endif
								</ul>
							@endif
						</div>		
						@if(in_array('11_Bulk Action', $moduleaccess) || $adminrole == 0)
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
	var chk = confirm('Are you sure! you want to delete this announcement?');
	if(chk) {
		window.location = "{{ url('/admin/announcement') }}/"+id+"/destroy";
	}
}
function chkupdatestatus(id, status) {
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this announcement?');
	if(chk) {
		window.location = "{{ url('/admin/announcement') }}/"+id+"/"+status+"/updatestatus";
	}
}
</script>	

