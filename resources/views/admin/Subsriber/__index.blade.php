@include('admin.includes.mainhead')



@include('admin.includes.maintopmenu')





<div id="container"> 

	

<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">

  

  <tr valign="top" bgcolor="#FFFFFF"> 

    <td height="500" width="100%" align="center" style=""><h2 style='color: #2b3d55;'><br></h2>



 <h1>Manage Subscriber</h1>

  



<table width="100%" border="0" cellspacing="0" cellpadding="3" align="center">

 <td width="70%">

</td>

<td width="30%"> 

<a class='blue_link'  href="{{ url('/admin/subscriber/bulkupdate') }}" ;>Export Data</a> 

|| <a href="{{ url('/admin/subscriber/create') }}" class="blue_link">Add new email</a></td>

</table>

			<form class="form form-horizontal" name="type_frm" id="type_frm" method = "post" action="{{ url('/admin/Subsriber/bulkupdate') }}" enctype="multipart/form-data">                                            

            {{ csrf_field() }}

			<div align="center" class="whitebox mtop15">

				<table width="80%" border="0" cellspacing="0" cellpadding="3" align="center" class="listing table table-striped dataex-html5-selectors" id="subscriber">

					<thead>

						<tr class="maincontentheading" height="25px"> 

							<th width="1%"></th>

							<th width="25%" style="text-align:left">Email ID</th>							

							<th width="35%" align="center">Actions</th>

						</tr>

					</thead>

					<tbody>

						@foreach($subscriber as $subscriber)

						<tr bgcolor="#CCCCCC" height="30px">

							<td></td>

							<td>{{ $subscriber->email }}</td>

							<td align="center" style="white-space:nowrap;">

                            <a href="javascript:void(0);" onclick="chkupdatestatus({{ $subscriber->id }}, {{ $subscriber->status }})">@if($subscriber->status == 0) Activate @else De-Activate @endif </a> &nbsp;&nbsp;|&nbsp;&nbsp;<a href="{{ url('/admin/Subsriber/'.$subscriber->id.'/edit') }}">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;

                            <a href="javascript:void(0);" onclick="chkdelete({{ $subscriber->id }})">Delete</a>

							</td>

						</tr>

						@endforeach

					</tbody>

				</table>

                <br>

			

			</div>	

			</form>



		</td>

		</tr>

	</tbody>

	</table>

</div>



@include('admin.includes.mainfooter')

<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>

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

<script>



$('.dataex-html5-selectors').DataTable( {

	dom: 'Bfrtip',

	buttons: [

		

	]

});



</script>

<style type="text/css">

.bktitle { font-weight: normal; }

</style>