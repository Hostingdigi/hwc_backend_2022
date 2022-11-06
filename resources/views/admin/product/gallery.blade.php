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

                            <h2 class="content-header-title float-left mb-0">Manage Product Gallery Image</h2>                            

                        </div>

						<div class="col-4" style="float:right; text-align:right;">

							<div class="dt-buttons btn-group"><a href="{{ url('/admin/products') }}" class="btn " tabindex="0" aria-controls="Products">

              <span><i class="feather icon-arrow-left"></i> Back</span></a> </div>

						</div>

                    </div>

                </div>                

            </div>
			  <section id="basic-horizontal-layouts">

                    <div class="row match-height">

                        <div class="col-md-12 col-12">

                            <div class="card">                               
                                <div class="card-content">

                                    <div class="card-body">

   			
	
	<form class="form form-horizontal" name="products_frm" id="products_frm" method = "post" action="{{ url('/admin/products/addgallery') }}" enctype="multipart/form-data">			
	<input type="hidden" name="id"  value="{{ $product->Id }}">			<input type="hidden" name="Status" value="1">	
	{{ csrf_field() }}				
	
	<div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Name </span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                {{ $product->EnName }}

                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Title</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                               <input type="text" name="Title" required value="" class="form-control"> 

                                                            </div>

                                                        </div>

                                                    </div>
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Picture &nbsp;(150px x 150px Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <div class="custom-file">

                                                                <input type="file" class="custom-file-input imageUpload" id="Image" name="Image">

                                                                <label class="custom-file-label" for="Image">Choose Image File</label>

                                                            </div>
																<div class="imageOutput"></div>

                                                            </div>

                                                        </div>

                                                    </div>
													 <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Large Picture (300px x 280px Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input largeimageUpload" id="LargeImage" name="LargeImage">

                                                                <label class="custom-file-label" for="LargeImage">Choose Large Image File</label>

                                                            </div>
																<div class="largeimageOutput"></div>

                                                            </div>

                                                        </div>

                                                    </div>   
													<div class="col-md-8 offset-md-4">

                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Add</button>

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
				<div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors" id="subscriber">
                            <thead>
                                <tr>
								<th>Title</th>
								<th>Image</th>
								<th>Display Order</th>
								<th>Actions</th>
								  </tr>
                            </thead>
                            <tbody>						
						  @foreach($galleries as $gallery)						
						  <tr>														
						  <td>{{ $gallery->Title }}</td>														
						  <td><img src="{{ url('/uploads/product/'.$gallery->Image) }}" width="100"></td>							
						  <td align="center"> </td>														
						  <td align="center" style="white-space:nowrap;">
						  <a href="javascript:void(0);" onclick="chkupdatestatus({{ $gallery->Id }}, {{ $gallery->Status }})">@if($gallery->Status == 1)
							<i class="fa fa-eye fa-lg" aria-hidden="true"></i>
						  @else 
							<i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i>
						  @endif </a>&nbsp;&nbsp;
						  <a href="javascript:void(0);" onclick="chkdelete({{ $gallery->Id }})">
								<i class="fa fa-trash fa-lg" aria-hidden="true"></i>
						  </td>						
						  </tr>						
						  @endforeach					
						  </tbody>				
						  </table>		
						 
						  </section>





            </div>

        </div>

    </div>



    <div class="sidenav-overlay"></div>

    <div class="drag-target"></div>   

@include('admin.includes.footer')</div>		


						  
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
$('.dataex-html5-selectors').DataTable({
	dom: 'Bfrtip',	buttons: [
	]
});
function chkdelete(id) {
	var chk = confirm('Are you sure! you want to delete this product gallery image?');
	if(chk) {
		window.location = "{{ url('/admin/products') }}/"+id+"/destroygallery";
	}
}

function chkupdatestatus(id, status){
	var statustext = 'Activate';
	if(status == 1) {
		statustext = 'De-Activate';
	}
	var chk = confirm('Are you sure! you want to '+statustext+' this product image?');
	if(chk) {
		window.location = "{{ url('/admin/products') }}/"+id+"/"+status+"/updategallerystatus";
	}
}
</script>
	
<script>

$images = $('.imageOutput')



$(".imageUpload").change(function(event){

    readURL(this);

});



function readURL(input) {



    if (input.files && input.files[0]) {

        

        $.each(input.files, function() {

            var reader = new FileReader();

            reader.onload = function (e) {           

                $images.html('<img src="'+ e.target.result+'" width="100"/>')

            }

            reader.readAsDataURL(this);

        });

        

    }

}

$largeimages = $('.largeimageOutput')



$(".largeimageUpload").change(function(event){

    largereadURL(this);

});



function largereadURL(input) {



    if (input.files && input.files[0]) {

        

        $.each(input.files, function() {

            var reader = new FileReader();

            reader.onload = function (e) {           

                $largeimages.html('<img src="'+ e.target.result+'" width="100"/>')

            }

            reader.readAsDataURL(this);

        });

        

    }

}

   

</script>