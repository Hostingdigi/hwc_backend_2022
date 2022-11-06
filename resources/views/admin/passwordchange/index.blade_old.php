@include('admin.includes.mainhead')

@include('admin.includes.maintopmenu')

    <!-- BEGIN: Header-->
<div id="container"> 
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
	<tbody>
	<tr valign="top" bgcolor="#FFFFFF"> 
		<td height="500" width="100%" align="center" style=""><h2 style="color: #2b3d55;"><br></h2>
		<h1>MANAGE MASTHEAD</h1>
		<div class="whitebox mtop15">
			<form class="form form-horizontal" name="passwordchange" id="passwordchange" method = "post" action="{{ url('/admin/passwordchange') }}" enctype="multipart/form-data">                                            
            {{ csrf_field() }}
            <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0" class="tableborder_new">
                <tr class="maincontentheading"> 
                  
            <td height="29" colspan="2" align='center' class='whitefont_header'> 
              Change Password</td>
                </tr>
                <tr valign="top" class="postaddcontent"> 
                  <td width="50%"><span class="whitefont">Old Password</span><span class="starcolor">*</span></td>
                  <td width="50%"> <input type="text" name="admin_pass" value=""> 
                  </td>
                </tr>
                
                <tr valign="top" class="postaddcontent"> 
                  <td><span class="whitefont">Password</span><span class="starcolor">*</span></td>
                  <td><input type="password" name="admin_password" value=""></td>
                </tr>
                <tr valign="top" class="postaddcontent"> 
                  <td><span class="whitefont">Confirm Password</span><span class="starcolor">*</span></td>
                  <td><input type="password" name="cadmin_password" value=""></td>
                </tr>
                <tr valign="top" class="postaddcontent"> 
              
                <td align="center" colspan="2"> 
            <a href="#">
            <img align="absmiddle" src="{{ asset('images/reset.jpg') }}" onClick="window.document.reset();" border="0">
            </a>&nbsp;&nbsp;&nbsp;&nbsp; 
            <input align="absmiddle" style="border:0px;" type="image" src="{{ asset('images/submit.jpg') }}" name="Submit" value="Submit">
            
				
                </td>
                </tr>
              </table>

           </form>
</div>
  
  </td>
</tr>
</table>

