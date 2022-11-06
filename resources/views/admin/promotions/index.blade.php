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

        <div class="content-wrapper" style="width:60%; margin:0 auto;">

            <div class="content-header row">

                <div class="content-header-left col-md-12 col-12 mb-2">

                    <div class="row breadcrumbs-top">

                        <div class="col-8">

                            <h2 class="content-header-title float-left mb-0">Manage Banner Master</h2>                            

                        </div>
						@if(in_array('9_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">

							<div class="dt-buttons btn-group"><a href="{{ url('/admin/promotions/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span>

                            <i class="feather icon-plus"></i> Add New</span></a> </div>

						</div>
						@endif
                    </div>

                </div>                

            </div>

	<form class="form form-horizontal" name="type_frm" id="type_frm" method = "post" action="{{ url('/admin/promotions/bulkupdate') }}" enctype="multipart/form-data"> 						
			{{ csrf_field() }}
			
			<div class="content-body">

			<section id="data-list-view" class="data-list-view-header">
			

                <div class="table-responsive">

                        <table class="table table-striped tble-bordered dataex-html5-selectors" id="subscriber">

                            <thead>

                                <tr style="background:#CCC;">

                                    <th>ID</th>

                                    <th>Image Title</th>                                                                        

                                    <th>Display Order</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                            @foreach($promotions as $promotion)

                                    <tr>
									<td>
									<input type="checkbox" value="{{ $promotion->ban_id }}" name="promotionsid[]">
									</td>
                                   

							        <td>{{ $promotion->ban_name }}</td>                            

                                    <td>
									<input type = "text" name="display_order{{ $promotion->ban_id }}" id="display_order{{ $promotion->ban_id }}" class="form-control col-md-3 display_order" value = "{{ $promotion->display_order }}">
							</td>                                                                              

                                        <td> 
										@if(in_array('9_Status', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $promotion->ban_id }}, {{ $promotion->ban_status }})">

										@if($promotion->ban_status != 0) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif 
										@endif
										
										@if(in_array('9_Edit', $moduleaccess) || $adminrole == 0)
										</a> &nbsp;&nbsp;|&nbsp;&nbsp;<a href="{{ url('/admin/promotions/'.$promotion->ban_id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('9_Delete', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkdelete({{ $promotion->ban_id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
										@endif
										</td>

                                    </tr>

                                @endforeach

                            </tbody>

                            </table>
							
					<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($promotions->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($promotions->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $promotions->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($promotions->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $promotions->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($promotions->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $promotions->lastPage()) as $i)
										@if($i >= $promotions->currentPage() - 2 && $i <= $promotions->currentPage() + 2)
											@if ($i == $promotions->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $promotions->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($promotions->currentPage() < $promotions->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($promotions->currentPage() < $promotions->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $promotions->appends($_GET)->url($promotions->lastPage()) }}">{{ $promotions->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($promotions->hasMorePages())
										<li><a href="{{ $promotions->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
									@else
										<li class="disabled"><span>»</span></li>
									@endif
								</ul>
							@endif
						</div>		
						@if(in_array('9_Bulk Action', $moduleaccess) || $adminrole == 0)
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

		window.location = "{{ url('/admin/promotions') }}/"+id+"/destroy";

	}

}

function chkupdatestatus(id, status) {

	var statustext = 'Activate';

	if(status == 1) {

		statustext = 'De-Activate';

	}

	var chk = confirm('Are you sure! you want to '+statustext+' this Static Page ?');

	if(chk) {

		window.location = "{{ url('/admin/promotions') }}/"+id+"/"+status+"/updatestatus";

	}

}
function bulk_update_item(val){
	if(val != '') {
	var del_process = 0;
	for(i=0; i < window.document.type_frm.elements.length; i++)
	{	

		if(window.document.type_frm.elements[i].name == "promotionsid[]")
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
