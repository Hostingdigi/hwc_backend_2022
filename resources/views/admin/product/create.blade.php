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

        <div class="content-wrapper">

            <div class="content-header row">

                <div class="content-header-left col-md-9 col-12 mb-2">

                    <div class="row breadcrumbs-top">

                        <div class="col-12">

                            <h2 class="content-header-title float-left mb-0">ADD PRODUCT</h2>

                            <div class="breadcrumb-wrapper col-12">

                                <ol class="breadcrumb">

                                    <li class="breadcrumb-item"><a href="#">HOME</a>

                                    </li>

                                    <li class="breadcrumb-item"><a href="#">PRODUCT</a>

                                    </li>

                                    <li class="breadcrumb-item active">ADD

                                    </li>

                                </ol>

                            </div>

                        </div>

                    </div>

                </div>                

            </div>

            <div class="content-body">



            <section id="basic-horizontal-layouts">

                    <div class="row match-height">

                        <div class="col-md-12 col-12">

                            <div class="card">

                                <div class="card-header">

                                    <h4 class="card-title">Add New Product</h4>

                                </div>

                                <div class="card-content">

                                    <div class="card-body">

                                        <form class="form form-horizontal" name="addproduct" id="addproduct" method = "post" action="{{ url('/admin/product') }}" enctype="multipart/form-data">

                                            

                                            {{ csrf_field() }}

                                            <div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Name <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="product_name" class="form-control" name="product_name" placeholder="Product Name" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>URL</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="url" class="form-control" name="url" placeholder="URL">

                                                            </div>

                                                        </div>

                                                    </div>                                                   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Code</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="code" class="form-control" name="code" placeholder="Code">

                                                            </div>

                                                        </div>

                                                    </div>  

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Category <span class="badge badge-dot badge-danger"> </span></span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="category" class="form-control" name="category" placeholder="Category" required>

                                                            </div>

                                                        </div>

                                                    </div>    

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Brand</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="brand" class="form-control" name="brand" placeholder="Brand">

                                                            </div>

                                                        </div>                                              

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Standard Price</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="standard_price" class="form-control" name="standard_price" placeholder="Standard Price">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Cost Price</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="cost_price" class="form-control" name="cost_price" placeholder="Cost Price">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Vendor</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="vendor" class="form-control" name="vendor" placeholder="Vendor">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>POC</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="poc" class="form-control" name="poc" placeholder="POC">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Color</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="color" class="form-control" name="color" placeholder="Color">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Sizes</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="sizes" class="form-control" name="sizes" placeholder="Sizes">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Specs</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea id="specs" class="form-control editor" name="specs" placeholder="Specs"></textarea>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Dimension</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="dimension" class="form-control" name="dimension" placeholder="Dimension">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>MOQ</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="moq" class="form-control" name="moq" placeholder="MOQ">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>UNSPSC Code</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="UNSPSC_Code" class="form-control" name="UNSPSC_Code" placeholder="UNSPSC Code">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Quantity</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="quantity" class="form-control" name="quantity" placeholder="Quantity">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Customer daily purchase limit</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="customer_daily_purchase_unit" class="form-control" name="customer_daily_purchase_unit" placeholder="Customer daily purchase limit">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Weight(in Kg)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <input type="text" id="weight" class="form-control" name="weight" placeholder="Weight">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Picture (150px x 150px Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input" id="small_image" name="small_image">

                                                                <label class="custom-file-label" for="small_image">Choose Image File</label>

                                                            </div>

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

                                                                <input type="file" class="custom-file-input" id="large_image" name="large_image">

                                                                <label class="custom-file-label" for="large_image">Choose Large Image File</label>

                                                            </div>

                                                            </div>

                                                        </div>

                                                    </div>        

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>TDS (Upload PDF & Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input" id="file_tds" name="file_tds">

                                                                <label class="custom-file-label" for="file_tds">Choose TDS PDF File</label>

                                                            </div>

                                                            </div>

                                                        </div>

                                                    </div>          

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>SDS (Upload PDF & Less then 1.5 MB)</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input" id="file_sds" name="file_sds">

                                                                <label class="custom-file-label" for="file_sds">Choose SDS PDF File</label>

                                                            </div>

                                                            </div>

                                                        </div>

                                                    </div>   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Featured Product ?</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="checkbox" id="is_featured_product" name="is_featured_product" value = "1">

                                                            </div>

                                                        </div>

                                                    </div>  

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Video Code</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="video_code" class="form-control" name="video_code" placeholder="Video Code">

                                                            </div>

                                                        </div>

                                                    </div> 

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Promotion ?</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="checkbox" id="is_promotion" name="is_promotion" value = "1">

                                                            </div>

                                                        </div>

                                                    </div>      

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Is Overseas Shipping Available ?</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="checkbox" id="is_overseas_shipping_available" name="is_overseas_shipping_available" value = "1">

                                                            </div>

                                                        </div>

                                                    </div>   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Content</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <textarea id="editor" class="form-control editor" name="content" placeholder="Content"></textarea>                                                               

                                                            </div>

                                                        </div>

                                                    </div>                                   

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Meta Title</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="meta_title" class="form-control" name="meta_title" placeholder="Meta Title">

                                                            </div>

                                                        </div>

                                                    </div>

                                                    

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Meta Keyword</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="meta_keyword" class="form-control" name="meta_keyword" placeholder="Meta Keyward">

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Meta Description</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="meta_description" class="form-control" name="meta_description" placeholder="Meta Description">

                                                            </div>

                                                        </div>

                                                    </div>                                                    

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                            <span>Status</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-control custom-switch custom-switch-warning mr-2 mb-1">

                                                            <input type="checkbox" class="custom-control-input" id="status" name = "status" checked>

                                                            <label class="custom-control-label" for="customSwitch7"></label>                                                                

                                                        </div>

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

                </section>





            </div>

        </div>

    </div>



    <div class="sidenav-overlay"></div>

    <div class="drag-target"></div>   

@include('admin.includes.footer')



<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>

<script>

	ClassicEditor

		.create( document.querySelector( '#editor' ), {

			// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]

		} )

		.then( editor => {

			window.editor = editor;

		} )

		.catch( err => {

			console.error( err.stack );

		} );

</script>