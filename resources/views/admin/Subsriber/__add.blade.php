@include('admin.includes.mainhead')



@include('admin.includes.maintopmenu')





<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">

  

  <tr valign="top" bgcolor="#FFFFFF"> 

    <td height="500" width="100%" align="center" style="">

     <h1>Manage Subscriber</h1>

<form method="post" enctype="multipart/form-data" name="subscriber_frm" action = "{{ url('/admin/subscriber') }}">

<table width="45%" border="0" cellspacing="0" cellpadding="2" align="center" class="tableborder_new">

 {{ csrf_field() }}

		 

          <tr valign="top"> 

            <td  height="30" width="30%" class="postaddcontent"><span class="whitefont">Name</span></td>

            <td width="7%"><input type="text"  name="name" value="" class="mediumtxtbox"> </td>

    </tr>

      <tr valign="top"> 

            <td  height="30" width="30%" class="postaddcontent"><span class="whitefont">Email</span><span class="starcolor">*</span></td>

            <td width="70%"><input type="text"  name="email" value="" class="mediumtxtbox"> </td>

    </tr>

    

     <tr valign="top"> 

            <td  height="30" width="30%" class="postaddcontent"><span class="whitefont">Contact No</span></td>

            <td width="70%"><input type="text"  name="ContactNo" value="" class="mediumtxtbox"> </td>

    </tr>

     

	        <tr valign="top" class="postaddcontent">

	          <td  height="30"><span class="whitefont">Status</span></td>

            <td><select name="status" class="mediumtxtbox1">

			<option value="0" selected>In-Active</option>

                <option value="1" >Active</option>

                

              </select></td>

              </tr>

    <tr valign="top" class="postaddcontent"> 

          <tr valign="top" class="postaddcontent"> 

            <td colspan="2"><div align="center"> 

            <input type="hidden" name="submit_action" value="save">

               <input type="hidden" name="date_entered" value="2020-10-22 17:41:08">

                <input type="hidden" name="date_modified" value="2020-10-22 17:41:08">

                <input type="hidden" name="id" value="">

              </div></td>

          </tr>

           <tr> 

            <td align="center" colspan="2"> 

            <a href="#">

            <img align="absmiddle" src="{{ asset('images/reset.jpg') }}" onClick="window.document.reset();" border="0">

            </a>&nbsp;&nbsp;&nbsp;&nbsp; 

            <input align="absmiddle" style="border:0px;" type="image" src="{{ asset('images/submit.jpg') }}" name="Submit" value="Submit">

            </td>

          </tr>

          <tr> 

            <td colspan="2" height="8px"> </td>

          </tr>

  </table>

</form>	

</td>

 </tr>

</table>

@include('admin.includes.mainfooter')