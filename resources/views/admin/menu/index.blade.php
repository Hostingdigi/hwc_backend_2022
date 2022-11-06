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

                            <h2 class="content-header-title float-left mb-0">Manage Menu</h2>                            

                        </div>

						<div class="col-4" style="float:right; text-align:right;">

							<div class="dt-buttons btn-group"><a href="{{ url('/admin/menu/create') }}" class="btn btn-outline-primary" tabindex="0" aria-controls="menu"><span><i class="feather icon-plus"></i> Add New</span></a> </div>

						</div>

                    </div>

                </div>                

            </div>

            <div class="content-body">

            <section id="data-list-view" class="data-list-view-header">

            <form class="form form-horizontal" name="type_frm" id="type_frm" method = "post" action="{{ url('/admin/menu/bulkupdate') }}" enctype="multipart/form-data"> 
			
				 <br><br>
			<div class="col-md-2" style="float:right; text-align:right;">
				<select name="bulk_action" id="bulk_action" class="form-control" onchange="bulk_update_item(this.value)">
						<option value="">Select </option>
						<option value="delete">Delete</option>
						<option value="update_disporder">Update Display Order</option>
				</select>
				</div>

                <div class="table-responsive">

                        <table class="table table-striped dataex-html5-selectors" id="menu">

                            <thead>

                                <tr>

                                    <th>ID</th>

                                    <th>Menu Name</th>   

									<th>Menu URL</th> 	

									<th>Display Order</th>	

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>

                               @foreach($menu as $menu)

                                    <tr>
										<td>
										<input type="checkbox" value="{{ $menu->id }}" name="menuid[]"></td>
                                        

                                        <td>{{ $menu->menuname }}</td> 

										<td>{{ $menu->menu_url }}</td> 
										
										<td><input type = "text" name="display_order{{ $menu->Id }}" id="display_order{{ $menu->Id }}" class="form-control col-md-3 display_order" value = "{{ $menu->display_order }}">
								</td>	

										<td> <a href="javascript:void(0);" onclick="chkupdatestatus({{ $menu->id }}, {{ $menu->status }})">@if($menu->status == 1) <i class="fa fa-eye fa-lg" aria-hidden="true"></i> @else <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i> @endif </a> &nbsp;&nbsp;|&nbsp;&nbsp;

										<a href="{{ url('/admin/menu/'.$menu->id.'/edit') }}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;

                            <a href="javascript:void(0);" onclick="chkdelete({{ $menu->id }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a></td>

                                    </tr>

                                @endforeach

                            </tbody>

                            </table>

					</form>


				</div>


            </section>

           
			

        </div>

</div>

    <div class="sidenav-overlay"></div>

    <div class="drag-target"></div>   

@include('admin.includes.footer')



<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('app-assets/js/scripts/ui/data-list-view.js') }}"></script>

<script type="text/javascript">



function chkdelete(id) {



	var chk = confirm('Are you sure! you want to delete this Menu ?');



	if(chk) {



		window.location = "{{ url('/admin/menu') }}/"+id+"/destroy";



	}



}



function chkupdatestatus(id, status) {



	var statustext = 'Activate';



	if(status == 1) {



		statustext = 'De-Activate';



	}



	var chk = confirm('Are you sure! you want to '+statustext+' this menu?');



	if(chk) {



		window.location = "{{ url('/admin/menu') }}/"+id+"/"+status+"/updatestatus";



	}



}

function bulk_update_item(val){

	var del_process = 0;

	for(i=0; i < window.document.type_frm.elements.length; i++)
	{		



		if(window.document.type_frm.elements[i].name == "menuid[]")



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



$('.dataex-html5-selectors').DataTable( {

 /*dom: 'lBfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [ 0, ':visible' ]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                text: 'JSON',
                action: function ( e, dt, button, config ) {
                    var data = dt.buttons.exportData();

                    $.fn.dataTable.fileSave(
                        new Blob( [ JSON.stringify( data ) ] ),
                        'Export.json'
                    );
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]*/
		
		 "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
	
});



</script>