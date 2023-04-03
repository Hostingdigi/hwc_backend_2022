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
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">UPDATE BRAND</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">HOME</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">BRAND</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit
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
                                    <h4 class="card-title">Edit Brand</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="editbrand" id="editbrand" method = "post" action="{{ url('/admin/brand/'.$editbrand->id ) }}" enctype="multipart/form-data">
                                            
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <input type="hidden" name="brand_id" value="{{ $brand->id }}">
                                                                <input type="text" id="brand_name" class="form-control" name="brand_name" placeholder="Brand Name" value="{{ $brand->brand_name }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>URL</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="brand_url" class="form-control" name="brand_url" placeholder="URL" value="{{ $brand->brand_url }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Description</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="description" class="form-control editor" name="description" placeholder="Description">{{ $brand->description }}</textarea>                                                               
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
                                                                <input type="file" class="custom-file-input" id="brand_image" name="brand_image">
                                                                <label class="custom-file-label" for="brand_image">Choose file</label>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Meta Title</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_title" class="form-control" name="meta_title" placeholder="Meta Title" value = "{{ $brand->meta_title }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Meta Keyword</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_keyword" class="form-control" name="meta_keyword" placeholder="Meta Keyward" value="{{ $brand->meta_keyword }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Meta Description</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="meta_description" class="form-control" name="meta_description" placeholder="Meta Description" value="{{ $brand->meta_description }}">
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
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
