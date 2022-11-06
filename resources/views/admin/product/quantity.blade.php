@include('admin.includes.header')


<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

<!-- END: Head-->

@include('admin.includes.topmenu')

<!-- BEGIN: Body-->

@include('admin.includes.mainmenu')
 <div class="app-content content" style="background-color:#fff;">
	
		     <div class="content-overlay"></div>

        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
       <div class="content-header row">

                <div class="content-header-left col-md-12 col-12 mb-2">

                    <div class="row breadcrumbs-top">

                        <div class="col-8">

                            <h2 class="content-header-title float-left mb-0">Manage Product Quantity</h2>                            

                        </div>

						<div class="col-4" style="float:right; text-align:right;">

							<div class="dt-buttons btn-group"><a href="{{ url('/admin/products') }}" class="btn " tabindex="0" aria-controls="Products">

              <span><i class="feather icon-arrow-left"></i> Back</span></a> </div>

						</div>

                    </div>

                </div>                

            </div>
			
			 <div class="content-body">



            <section id="basic-horizontal-layouts">

                    <div class="row match-height">

                        <div class="col-md-12 col-12">

                            <div class="card">                               
                                <div class="card-content">

                                    <div class="card-body">
									<form class="form form-horizontal" name="products_frm" id="products_frm" method = "post" action="{{ url('/admin/products/updatequantity') }}" enctype="multipart/form-data">			
									<input type="hidden" name="id" value="{{ $product->Id }}">			
									<input type="hidden" name="remaining_qty" value="{{ $product->Quantity }}">   
                                            

                                            {{ csrf_field() }}

                                            <div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Name <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                {{ $product->EnName }}

                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">
                                                                <span>Total Quantity </span>
                                                            </div>

                                                            <div class="col-md-8">
                                                                {{ $product->Quantity }}
                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">
                                                                <span>Purchased Quantity</span>
                                                            </div>

                                                            <div class="col-md-8">
                                                                0
                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">
                                                                <span>Remaining Quantity</span>
                                                            </div>

                                                            <div class="col-md-8">
                                                               {{ $product->Quantity }}
                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">
                                                                <span>Add Extra(Quantity)</span>
                                                            </div>

                                                            <div class="col-md-8">
                                                                <input type="text" id="Quantity" class="form-control" name="Quantity" placeholder="Quantity">

                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-md-8 offset-md-4">

                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Save</button>

                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>

                                                    </div>

                                                </div>

                                            </div>

                                        </form>

                                    </div>

                                </div>

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




 
<td height="30" width="30%" class="postaddcontent"><span class="whitefont">Total Quantity</span></td>          
<td width="70%">{{ $product->Quantity }}</td>    	 </tr>        <tr valign="top">           
<td height="30" width="30%" class="postaddcontent">
<span class="whitefont">Purchased Quantity</span></td>          
<td width="70%">0</td>    	 </tr>		 <tr valign="top">            
<td height="30" width="30%" class="postaddcontent"><span class="whitefont">Remaining Quantity</span></td>          
<td width="70%">{{ $product->Quantity }}</td>    	 </tr>		 
<tr valign="top">             <td height="30" width="30%" class="postaddcontent">
<span class="whitefont">Add Extra(Quantity)</span></td>            <td width="70%">
<input type="text" name="Quantity" value="" class="mediumtxtbox">            </td>    	 </tr>             <tr valign="top" class="postaddcontent">           </tr><tr valign="top" class="postaddcontent">             
<td colspan="2"><div align="center">                 
<input type="hidden" name="submit_action" value="save">                                            
</div></td>          </tr>          <tr>            
<td align="center" colspan="2"><a href="#"><img align="absmiddle" src="{{ url('images/reset.jpg') }}" onclick="window.document.products_frm.reset();" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;             
<input type="image" src="{{ url('/images/submit.jpg') }}" name="Submit" border="0" style="border:0px;" align="absmiddle">               <input type="hidden" value="0" name="boolcheck"> </td>          </tr>          <tr>            
<td colspan="2" height="8px"> </td>          </tr>  </tbody></table>			</form>		</div>		</td>	</tr>	</tbody></table></div> 
@include('admin.includes.mainfooter')
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>	ClassicEditor		.create( document.querySelector( '#editor' ), {			// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]            height: 500		} )		.then( editor => {			window.editor = editor;		} )		.catch( err => {			console.error( err.stack );		} );   </script>