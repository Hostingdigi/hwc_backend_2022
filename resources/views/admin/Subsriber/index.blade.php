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
        <div class="content-wrapper" style="width:70%; margin:0 auto;">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Subscriber</h2>                            
                        </div>
						@if(in_array('24_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/subscriber/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span><i class="feather icon-plus"></i> Add New</span></a> </div>
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
								<th>Id</th>
								<th>EMAIL ID</th>                                                                        
								<th>ACTION</th>
							</tr>
						</thead>
						<tbody>
						@if(count($subscribers) > 0)
						   @foreach($subscribers as $subscriber)
								<tr>
									<td>{{ $subscriber->id }}</td>
									<td>{{ $subscriber->email }}</td>
									<td> 
									@if(in_array('24_Status', $moduleaccess) || $adminrole == 0)
									<a href="javascript:void(0);" onclick="chkupdatestatus({{ $subscriber->id }}, {{ $subscriber->status }})">
									@if($subscriber->status == 1) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;|&nbsp;&nbsp;
									@endif
									
									@if(in_array('24_Edit', $moduleaccess) || $adminrole == 0)
									<a href="{{ url('/admin/subscriber/'.$subscriber->id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
									@endif
									
									@if(in_array('24_Delete', $moduleaccess) || $adminrole == 0)									
									<a href="javascript:void(0);" onclick="chkdelete({{ $subscriber->id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
									@endif
								</td>
								</tr>
							@endforeach
						@else
								<tr><td colspan="3" align="center">No Records Found</td></tr>
						@endif	
						</tbody>
					</table>
				</div>
				<div class="row" style="padding:10px 0;">
					<div class="col-md-10">
						@if ($subscribers->hasPages())
							<ul class="pagination pagination">
								{{-- Previous Page Link --}}
								@if ($subscribers->onFirstPage())
									<li class="disabled"><span>«</span></li>
								@else
									<li><a href="{{ $subscribers->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
								@endif

								@if($subscribers->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $subscribers->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($subscribers->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $subscribers->lastPage()) as $i)
									@if($i >= $subscribers->currentPage() - 2 && $i <= $subscribers->currentPage() + 2)
										@if ($i == $subscribers->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $subscribers->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($subscribers->currentPage() < $subscribers->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($subscribers->currentPage() < $subscribers->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $subscribers->appends($_GET)->url($subscribers->lastPage()) }}">{{ $subscribers->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($subscribers->hasMorePages())
									<li><a href="{{ $subscribers->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
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

	var chk = confirm('Are you sure! you want to delete this Subcriber?');

	if(chk) {

		window.location = "{{ url('/admin/subscriber') }}/"+id+"/destroy";

	}

}

function chkupdatestatus(id, status) {

	var statustext = 'Activate';

	if(status == 1) {

		statustext = 'De-Activate';

	}

	var chk = confirm('Are you sure! you want to '+statustext+' this subscriber?');

	if(chk) {

		window.location = "{{ url('/admin/subscriber') }}/"+id+"/"+status+"/updatestatus";

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
