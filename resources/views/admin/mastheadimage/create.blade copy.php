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
		<h1>MANAGE PAGE CONTENT</h1>
		<div class="whitebox mtop15">
			<form class="form form-horizontal" name="static_pages" id="static_pages" method = "post" action="{{ url('/admin/static_pages') }}" enctype="multipart/form-data">                                            
            {{ csrf_field() }}
            <table width="80%" border="0" cellspacing="0" cellpadding="2" align="center" >

    
<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Page Title </span><span class="starcolor">*</span></td>
  <td width="60%"><input type="text" name="EnTitle" style="width:400px" value="" class="mediumtxtbox">  </td>
</tr>

  <tr valign="top"> 
      <td  height="30" width="30%" class="postaddcontent"><span class="whitefont">Menu notes</span></td>
      <td width="70%"><textarea name="ShortDesc" cols="30" rows="1"></textarea>  </td>
   </tr>

<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Parent Type </span><span class="starcolor">*</span></td>
  <td width="60%"><select name="parent_id" class="mediumtxtbox1" >
  <option value="0"  selected="selected">Top Level</option>
              <option value="22" >.</option>
              <option value="24" >404 Page</option>
              <option value="12" >About HardwareCity®</option>
              <option value="6" >By Brands</option>
              <option value="3" >By Category</option>
              <option value="2" >Collection Centre</option>
              <option value="5" >Contact Us</option>
              <option value="13" >Corporate</option>
              <option value="23" >e-Procurement PunchOUT</option>
              <option value="17" >FAQ (How To Pay)</option>
              <option value="16" >Feedback</option>
              <option value="11" >Footer Menu</option>
              <option value="1" >Home</option>
              <option value="9" >Privacy Policy</option>
              <option value="7" >Promotions</option>
              <option value="4" >Services</option>
              <option value="18" >Shipping Policy</option>
              <option value="8" >Site Map</option>
              <option value="21" >Store Locator</option>
              <option value="14" >Support</option>
              <option value="10" >Terms and Conditions</option>
              <option value="19" >Thank you</option>
              <option value="20" >Thank you for your subscription</option>
              </select></td>
</tr>
<!-- <tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Header </span><span class="starcolor">*</span></td>
  <td width="60%"><textarea name="page_header" style="width:400px" rows="4"  class="mediumtxtbox"> </textarea>  </td>
</tr> -->
<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Page URL  </span><span class="starcolor">*</span></td>
  <td width="60%">index/<input type="text" name="UniqueKey_temp" style="width:370px" value="" class="mediumtxtbox">   </td>
</tr>
<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Menu Type </span><span class="starcolor">*</span></td>
  <td width="60%"><select name="menu_type" class="mediumtxtbox1">
  <option value="0" selected>Not in menu</option>
  <option value="3" >Main and Footer Menu</option>
  <option value="4" >Submenu</option>
  <option value="1" >Main menu</option>  
  <option value="2" >Footer menu</option>   
  <option value="5" >Top menu</option> 
  <option value="6" >Block</option>               
    </select></td>
</tr>
<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Banner Type</span><span class="starcolor">*</span></td>
  <td width="60%"><input type="radio" name="banner_type"  value="1" />&nbsp;Flash &nbsp;&nbsp;<input type="radio" name="banner_type" value="2" checked="checked" />&nbsp;Image</td>
</tr>
<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Banner Image</span><span class="starcolor">*</span></td>
  <td width="60%"><input type="file" name="banner_image" style="width:400px"/>
        </tr>


   <tr valign="top" class="postaddcontent">
     <td><span class="whitefont">English Content</span></td>
     <td>&nbsp;</td>
</tr>
   <tr valign="top" style="margin-bottom: 100px"> 
  <td colspan="2" align="center" style="margin-bottom: 100px; padding-bottom: 100px;">
  <textarea id = "editor" name="EnContent"> </textarea>
    <input type="hidden" id="EnContent" name="EnContent" value="" style="display:none" />
    <input type="hidden" id="EnContent___Config" value="" style="display:none" /></td>
</tr>
 

<tr valign="top" style="padding-top: 100px"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">SEO Page Title</span><span class="starcolor">*</span></td>
  <td width="60%"><textarea name="meta_title" cols="60" rows="2"></textarea>
</tr>

<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">SEO Meta Keywords</span><span class="starcolor">*</span></td>
  <td width="60%"><textarea name="meta_keywords" cols="60" rows="3"></textarea>
</tr>

<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">SEO Meta Descriptions</span><span class="starcolor">*</span></td>
  <td width="60%"><textarea name="meta_description" cols="60" rows="4"></textarea>
</tr>
  
  <tr valign="top" class="postaddcontent">
    <td  height="30"><span class="whitefont">Status</span></td>
  <td><select name="display_status" class="mediumtxtbox1">
  <option value="0" >In-Active</option>
      <option value="1" selected >Active</option>
      
    </select></td>
 </tr>
<tr valign="top"> 
  <td  height="30" width="40%" class="postaddcontent"><span class="whitefont">Rights to edit</span><span class="starcolor">*</span></td>
  <td width="60%"><select name="rights_to_visible" class="mediumtxtbox1">
  <option value="1">Superadmin Only</option>
              <option value="2" >hardwarecityonline</option>  
              <option value="5" >subadmin</option>  
              <option value="6" >pioneer</option>  
              <option value="8" >superstore</option>  
              <option value="9" >cckadmin</option>  
             
    </select></td>
</tr>
<tr valign="top" class="postaddcontent"> 
<tr valign="top" class="postaddcontent"> 
  <td colspan="2"><div align="center"> 
      <input type="hidden" name="submit_action" value="save">
     <input type="hidden" name="date_entered" value="2020-10-23 01:59:22">
      <input type="hidden" name="date_modified" value="2020-10-23 01:59:22">
      <input type="hidden" name="Id" value="">
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
</div>
  
  </td>
</tr>
</table>
@include('admin.includes.mainfooter')
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>
	ClassicEditor
		.create( document.querySelector( '#editor' ), {
			// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
            height: 500
		} )
		.then( editor => {
			window.editor = editor;
		} )
		.catch( err => {
			console.error( err.stack );
		} );
   
</script>
<!--script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script-->
