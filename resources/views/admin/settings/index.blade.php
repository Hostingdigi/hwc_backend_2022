@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 300px;
}
</style>
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
                            <h2 class="content-header-title float-left mb-0">Site Configuration</h2>                            
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
                                    <form class="form form-horizontal" name="settings" id="settings" method = "post" action="{{ url('/admin/subadmin_settings/'.$settings->id) }}" enctype="multipart/form-data">                                            			
			                            <input type="hidden" name="id" value="{{ $settings->id }}">			
										<input type="hidden" name="_method" value="PUT">
                                      {{ csrf_field() }}  

                                      <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Website Path</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="site_path" class="form-control" name="site_path" value="{{ $settings->site_path }}" placeholder="Site Path" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Download Path</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="download_path" class="form-control" name="download_path" value="{{ $settings->download_path }}" placeholder="Download Path" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Company Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="company_name" class="form-control" name="company_name" value="{{ $settings->company_name }}" placeholder="Company Name" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Company Address</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                               <textarea id="company_address" class="form-control" name="company_address">{{ $settings->company_address }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Company Phone</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="company_phone" class="form-control" name="company_phone" value="{{ $settings->company_phone }}" placeholder="Company Phone" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Company Fax</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="company_fax" class="form-control" name="company_fax" value="{{ $settings->company_fax }}" placeholder="Company Fax" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>GST Registration No</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="GST_res_no" class="form-control" name="GST_res_no" value="{{ $settings->GST_res_no }}" placeholder="GST Registration No" required>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Admin Email id</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="admin_email" class="form-control" name="admin_email" value="{{ $settings->admin_email }}" placeholder="Admin Email id" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Enquiries Email id</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="enquiries_email" class="form-control" name="enquiries_email" value="{{ $settings->enquiries_email }}" placeholder="Enquiries Email id" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>CarbonCopy Email id</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="cc_email" class="form-control" name="cc_email" value="{{ $settings->cc_email }}" placeholder="CarbonCopy Email id" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Blind Carboncopy Email id</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="bcc_email" class="form-control" name="bcc_email" value="{{ $settings->bcc_email }}" placeholder="Blind Carboncopy Email id" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Date Format</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="date_format" class="form-control" name="date_format" value="{{ $settings->date_format }}" placeholder="Date Format" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Website Title</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="site_title" class="form-control" name="site_title" value="{{ $settings->site_title }}" placeholder="Website Title" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Database Server Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="database_server" class="form-control" name="database_server" value="{{ $settings->database_server }}" placeholder="Database Server Name" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Database Username</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="database_username" class="form-control" name="database_username" value="{{ $settings->database_username }}" placeholder="Database Username" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Database Password</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="database_password" class="form-control" name="database_password" value="{{ $settings->database_password }}" placeholder="Database Password" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Database Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="database_name" class="form-control" name="database_name" value="{{ $settings->database_name }}" placeholder="Database Name" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Minimum Quqntity for notification</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="notify_min_prod_qty" class="form-control" name="notify_min_prod_qty" value="{{ $settings->notify_min_prod_qty }}" placeholder="Minimum Quqntity for notification" required> count
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Image File Types</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="img_files_allowed_ext" class="form-control" name="img_files_allowed_ext" value="{{ $settings->img_files_allowed_ext }}" placeholder="Image File Types" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Other File Types</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="files_allowed_ext" class="form-control" name="files_allowed_ext" value="{{ $settings->files_allowed_ext }}" placeholder="Other File Types" required>
                                                            </div>
                                                        </div>
                                                    </div>
													 <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>File Size</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="files_allowed_size" class="form-control" name="files_allowed_size" value="{{ $settings->files_allowed_size }}" placeholder="File Height" required> (bytes)
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>File Height</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="files_allowed_h" class="form-control" name="files_allowed_h" value="{{ $settings->files_allowed_h }}" placeholder="File Height" required> (px)
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>File Width</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="files_allowed_w" class="form-control" name="files_allowed_w" value="{{ $settings->files_allowed_w }}" placeholder="File Width" required> (px)
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price1</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="price1" class="form-control" name="price1" value="{{ $settings->price1 }}" placeholder="Price1" required> USD
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price2</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="price2" class="form-control" name="price2" value="{{ $settings->price2 }}" placeholder="Price2" required> USD
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price3</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="price3" class="form-control" name="price3" value="{{ $settings->price3 }}" placeholder="Price3" required> USD
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price4</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="price4" class="form-control" name="price4" value="{{ $settings->price4 }}" placeholder="Price4" required> USD
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>No.of.News to display</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="noofnews" class="form-control" name="noofnews" value="{{ $settings->noofnews }}" placeholder="No.of.News to display" required> numbers
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Product Weight for shipping</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="prod_weight" class="form-control" name="prod_weight" value="{{ $settings->prod_weight }}" placeholder="Product Weight for shipping" required> g
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1" name="Submit" value="Submit">Save</button>
                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
                                                    </div>
                                                  </div>
                                                  </div>
                                          </form>
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