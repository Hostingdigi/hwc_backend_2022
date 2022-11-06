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
                            <h2 class="content-header-title float-left mb-0">Manage Email Template</h2>                            
                        </div>
						@if(in_array('12_Add', $moduleaccess) || $adminrole == 0)
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/emailtemplate/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="categories"><span><i class="feather icon-plus"></i> Add New</span></a> </div>
						</div>
						@endif
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
            
                <div class="table-responsive">
                        <table class="table table-striped table-bordered dataex-html5-selectors" id="emailtemplate">
                            <thead>
                                <tr style="background:#CCC;">
                                    <th></th>
                                    <th>Template Name</th>
                                    <th>Subject</th>                                                                   
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($emailtemplates as $emailtemplate)
                                    <tr>
                                        <td>{{ $emailtemplate->id }}</td>                                       
                                        <td>{{ $emailtemplate->templatename }}</td>                                                                              
                                        <td>{{ $emailtemplate->subject }}</td>
                                        <td> 
										@if(1 == 2)
										<a href="javascript:void(0);" onclick="chkupdatestatus({{ $emailtemplate->id }}, {{ $emailtemplate->status }})">
										@if($emailtemplate->status == 1) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('12_Edit', $moduleaccess) || $adminrole == 0)
										<a href="{{ url('/admin/emailtemplate/'.$emailtemplate->id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a></a>&nbsp;&nbsp;|&nbsp;&nbsp;
										@endif
										
										@if(in_array('12_Delete', $moduleaccess) || $adminrole == 0)
										<a href="javascript:void(0);" onclick="chkdelete({{ $emailtemplate->id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
										@endif
										</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>

					<div class="row" style="padding:10px 0;">
						<div class="col-md-9">
							@if ($emailtemplates->hasPages())
								<ul class="pagination pagination">
									{{-- Previous Page Link --}}
									@if ($emailtemplates->onFirstPage())
										<li class="disabled"><span>«</span></li>
									@else
										<li><a href="{{ $emailtemplates->appends($_GET)->previousPageUrl() }}" rel="prev">«</a></li>
									@endif

									@if($emailtemplates->currentPage() > 3)
										<li class="hidden-xs"><a href="{{ $emailtemplates->appends($_GET)->url(1) }}">1</a></li>
									@endif
									@if($emailtemplates->currentPage() > 4)
										<li><span>...</span></li>
									@endif
									@foreach(range(1, $emailtemplates->lastPage()) as $i)
										@if($i >= $emailtemplates->currentPage() - 2 && $i <= $emailtemplates->currentPage() + 2)
											@if ($i == $emailtemplates->currentPage())
												<li class="active"><span>{{ $i }}</span></li>
											@else
												<li><a href="{{ $emailtemplates->appends($_GET)->url($i) }}">{{ $i }}</a></li>
											@endif
										@endif
									@endforeach
									@if($emailtemplates->currentPage() < $emailtemplates->lastPage() - 3)
										<li><span>...</span></li>
									@endif
									@if($emailtemplates->currentPage() < $emailtemplates->lastPage() - 2)
										<li class="hidden-xs"><a href="{{ $emailtemplates->appends($_GET)->url($emailtemplates->lastPage()) }}">{{ $emailtemplates->lastPage() }}</a></li>
									@endif

									{{-- Next Page Link --}}
									@if ($emailtemplates->hasMorePages())
										<li><a href="{{ $emailtemplates->appends($_GET)->nextPageUrl() }}" rel="next">»</a></li>
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

	var chk = confirm('Are you sure! you want to delete this Email Template?');

	if(chk) {

		window.location = "{{ url('/admin/emailtemplate') }}/"+id+"/destroy";

	}

}

function chkupdatestatus(id, status) {

	var statustext = 'Activate';

	if(status == 1) {

		statustext = 'De-Activate';

	}

	var chk = confirm('Are you sure! you want to '+statustext+' this Email Template ?');

	if(chk) {

		window.location = "{{ url('/admin/emailtemplate') }}/"+id+"/"+status+"/updatestatus";

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
