@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
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
        <div class="content-wrapper" style="width:60%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Admin </h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/manageadmin') }}" class="btn " tabindex="0" aria-controls="banner_master">
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
                                    <form class="form form-horizontal" name="manageadmin" id="manageadmin" method = "post" action="{{ url('/admin/manageadmin/'.$manageadmin->id) }}" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{ $manageadmin->id }}">			
									<input type="hidden" name="_method" value="PUT"> 
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Admin Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" name="admin_uname" value="{{ $manageadmin->username }}" class="form-control" placeholder="Admin Name" required> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Priority</span>
                                                            </div>
                                                            <div class="col-md-8">                                               
                                                                <select name="priority" class="form-control" required>
                                                                    <option value="">Select Role</option>
                                                                    @if($rolerights)
																		@foreach($rolerights as $roleright)
																			<option value="{{ $roleright->id }}" @if($roleright->id == $manageadmin->priority) selected @endif>{{ $roleright->role_name }}</option>
																		@endforeach
																	@endif	
                                                                </select>
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Password</span>
                                                            </div>
                                                            <div class="col-md-8">                                                                
                                                            <input type="password" name="admin_password" value="" class="form-control" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Confirm Password</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <input type="password" name="cadmin_password" value="" class="form-control" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1" name="submit_action">Save</button>
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
