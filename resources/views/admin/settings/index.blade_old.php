@include('admin.includes.mainhead')

@include('admin.includes.maintopmenu')
<style>
.ck-editor__editable {
    min-height: 500px;
}
</style>
    <!-- BEGIN: Header-->
<div id="container"> 
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
	<tbody>
	<tr valign="top" bgcolor="#FFFFFF"> 
		<td height="500" width="100%" align="center" style=""><h2 style="color: #2b3d55;"><br></h2>
		<h1>SITE CONFIGURATION</h1>
		<div class="whitebox mtop15">
			<form class="form form-horizontal" name="settings" id="settings" method = "post" action="{{ url('/admin/subadmin_settings/'.$settings->id) }}" enctype="multipart/form-data">                                            			
			<input type="hidden" name="id" value="{{ $settings->id }}">			
			
            {{ csrf_field() }}
            <table width="98%" border="0" align="center" cellpadding="5" cellspacing="0" class="tableborder_new">
         
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Website Path</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="site_path" value="{{ $settings->site_path }}">            </td>
         </tr>
       <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Download Path</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="download_path" value="{{ $settings->download_path }}">            </td>
         </tr>          <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Company Name</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="company_name" value="{{ $settings->company_name }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td><span class="whitefont">Company Address</span><span class="starcolor">*</span></td>
           <td><textarea name="company_address" cols="35" rows="4">{!! $settings->company_address !!}</textarea></td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td><span class="whitefont">Company Phone</span><span class="starcolor">*</span></td>
           <td><input type="text" name="company_phone" value="{{ $settings->company_phone }}"></td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td><span class="whitefont">Company Fax</span><span class="starcolor">*</span></td>
           <td><input type="text" name="company_fax" value="{{ $settings->company_fax }}"></td>
         </tr>
          <tr valign="top" class="postaddcontent"> 
           <td><span class="whitefont">GST Registration No</span><span class="starcolor">*</span></td>
           <td><input type="text" name="GST_res_no" value="{{ $settings->GST_res_no }}"></td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Admin Email id</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="admin_email" value="{{ $settings->admin_email }}">            </td>
         </tr>
          <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Enquiries Email id</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="enquiries_email" value="{{ $settings->enquiries_email }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">CarbonCopy Email id</span></td>
           <td width="50%"> <input type="text" name="cc_email" value="{{ $settings->cc_email }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Blind Carboncopy Email id</span></td>
           <td width="50%"> <input type="text" name="bcc_email" value="{{ $settings->bcc_email }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Date Format</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="date_format" value="{{ $settings->date_format }}">            </td>
         </tr>
          <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Website Title</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="site_title" value="{{ $settings->site_title }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Database Server Name</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="database_server" value="{{ $settings->database_server }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Database Username</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="database_username" value="{{ $settings->database_username }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Database Password</span></td>
           <td width="50%"> <input type="text" name="database_password" value="{{ $settings->database_password }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Database Name</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="database_name" value="{{ $settings->database_name }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Minimum Quqntity for notification</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="notify_min_prod_qty" value="{{ $settings->notify_min_prod_qty }}">
            count</td></tr>
         <tr valign="top" class="postaddcontent"> 
           <td><span class="whitefont">Image File Types</span><span class="starcolor">*</span></td>
           <td> <input type="text" name="img_files_allowed_ext" value="{{ $settings->img_files_allowed_ext }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td><span class="whitefont">Other File Types</span><span class="starcolor">*</span></td>
           <td> <input type="text" name="files_allowed_ext" value="{{ $settings->files_allowed_ext }}">            </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">File Size</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="files_allowed_size" value="{{ $settings->files_allowed_size }}">
             (bytes) </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">File Height</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="files_allowed_h" value="{{ $settings->files_allowed_h }}">
             (px) </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">File Width</span><span class="starcolor">*</span></td>
           <td width="50%"> <input type="text" name="files_allowed_w" value="{{ $settings->files_allowed_w }}">
             (px) </td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Price1</span><span class="starcolor"><strong>(To search product by user)</strong>*</span></td>
           <td width="50%"> <input type="text" name="price1" value="{{ $settings->price1 }}">
            USD</td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Price2</span><span class="starcolor"><strong>(To search product by user)</strong>*</span></td>
           <td width="50%"> <input type="text" name="price2" value="{{ $settings->price2 }}">
            USD</td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Price3</span><span class="starcolor"><strong>(To search product by user)</strong>*</span></td>
           <td width="50%"> <input type="text" name="price3" value="{{ $settings->price3 }}">
            USD</td>
         </tr>
         <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Price4</span><span class="starcolor"><strong>(To search product by user)</strong>*</span></td>
           <td width="50%"> <input type="text" name="price4" value="{{ $settings->price4 }}">
            USD</td></tr>
       <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">No.of.News to display</span><span class="starcolor"><strong>(User section)</strong>*</span></td>
           <td width="50%"> <input type="text" name="noofnews" value="{{ $settings->noofnews }}">
            numbers</td></tr>
       <tr valign="top" class="postaddcontent"> 
           <td width="50%"><span class="whitefont">Product Weight for shipping</span><span class="starcolor"><strong>(User section)</strong>*</span></td>
           <td width="50%"> <input type="text" name="prod_weight" value="{{ $settings->prod_weight }}">
            g</td></tr>
                   <tr valign="top" class="postaddcontent"> 
           <td colspan="2"><div align="center"> 
           <input align="absmiddle" style="border:0px;" type="image" src="{{ asset('images/submit.jpg') }}" name="Submit" value="Submit">
               
             </div></td>
         </tr>
       </table>	  

     </td>

   </tr>

   <tr> 

           <td align="center">



           </td>

   </tr>

           </form>
</div>
  
  </td>
</tr>
</table>