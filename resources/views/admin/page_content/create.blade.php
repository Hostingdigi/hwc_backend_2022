@include('admin.includes.header')

<body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="semi-dark-layout">

<!-- END: Head-->

@include('admin.includes.topmenu')

<!-- BEGIN: Body-->

@include('admin.includes.sidemenu')

    <!-- BEGIN: Header-->

    <div class="app-content content" style="background-color:#fff;">

        <div class="content-overlay"></div>

        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">            

            <div class="content-body">

            <section id="basic-horizontal-layouts">

                    <div class="row match-height">

                        <div class="col-md-12 col-12">

                            <div class="card">

                                <div class="card-header">

                                    <h4 class="card-title">Add New Page</h4>

                                </div>

                                <div class="card-content">

                                    <div class="card-body">

                                        <form class="form form-horizontal" name="page_content" id="page_content" method = "post" action="{{ url('/admin/page_content') }}" enctype="multipart/form-data">                                            

                                            {{ csrf_field() }}

                                            <div class="form-body">

                                                <div class="row">

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Page Title</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="page_title" class="form-control" name="page_title" placeholder="Page Name" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Menu Notes</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="menu_notes" class="form-control" name="menu_notes" placeholder="Menu Notes" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Page Url</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                                <input type="text" id="page_url" class="form-control" name="page_url" placeholder="Page Url" required>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Parent Type</span>

                                                            </div>

                                                           

                                                            <div class="col-md-8">

                                                            <select name= "parent_type" class="form-control" id = "parent_type" required>

                                                                    <option value="1">Top Level</option>

                                                                    <option value="2"></option>

                                                                </select>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Menu Type</span>

                                                            </div>

                                                           

                                                            <div class="col-md-8">

                                                            <select name= "menu_type" class="form-control" id = "menu_type" required>

                                                                    <option value="1">Not in Menu</option>

                                                                    <option value="2">Main and Footer Menu</option>

                                                                    <option value="3">Sub Menu</option>

                                                                    <option value="4">Main Menu</option>

                                                                    <option value="5">Footer Menu</option>
																	
																	<option value="6">Footer Bottom Menu</option>

                                                                </select>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Banner Type</span>

                                                            </div>

                                                           

                                                            <div class="col-md-4 ">

                                                            <input type="radio" name="banner_type" class="" id="banner_type" value="1">

                                                            <label class="" for="banner_type">Flash</label>

                                                            </div>

                                                            <div class="col-md-4">

                                                            <input type="radio" name="banner_type" class="" id="banner_type" value="2"> 

                                                            <label class="" for="banner_type">Image</label>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Image</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <div class="custom-file">

                                                                <input type="file" class="custom-file-input" id="banner_image" name="banner_image">

                                                                <label class="custom-file-label" for="banner_image">Choose file</label>

                                                            </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Content</span>

                                                            </div>

                                                            <div class="col-md-8">

                                                            <textarea name="content" id="editor" class="form-control" style="height:200px;"></textarea>

                                                            </div>

                                                        </div>

                                                    </div>



                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                            <span>Status</span>

                                                            </div>

                                                            <div class="col-md-8">

															<select name="status" id="status" class="form-control">

																<option value="1">Active</option>

																<option value="0">In-Active</option>

                                                            </select>    

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



<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>

<script>

	 CKEDITOR.replace( 'editor' );
</script>

<!--script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script-->

