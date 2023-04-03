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
        <div class="content-wrapper" style="width:60%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">MANAGE ADMIN CHANGE PASSWORD</h2>                            
                        </div>				
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
						@if(Session::get('success'))
						<div class="alert success" style="color:green; text-align:center;">{{ Session::get('success') }}</div>
						@endif
						@if(Session::get('error'))
						<div class="alert dancer" style="color:red; text-align:center;">{{ Session::get('error') }}</div>
						@endif
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Change Password</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="adminaccess" id="adminaccess" method = "post" action="{{ url('/admin/passwordchange') }}" enctype="multipart/form-data" onsubmit="return validateform();">
										<input type="hidden" name="adminid" value="{{ $adminid }}">
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row{{ $errors->has('oldpassword') ? ' has-error' : '' }}">
                                                            <div class="col-md-4">
                                                                <span>Old Password</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="password" id="oldpassword" class="form-control" name="oldpassword" placeholder="Old Password" required>
                                                               
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row{{ $errors->has('newpassword') ? ' has-error' : '' }}">
                                                            <div class="col-md-4">
                                                                <span>New Password</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="password" id="newpassword" class="form-control" name="newpassword" placeholder="New Password" required>
                                                               
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Confirm Password</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="password" id="confirmpassword" class="form-control" name="confirmpassword" placeholder="Confirm Password" required>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                
                                                            </div>
                                                            <div class="col-md-8">
                                                               <div class="alert" id="passerror" style="display:none; color:red; ">New Password confirmation does not match!</div>
                                                            </div>
                                                        </div>
                                                    </div>
													
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Change</button>
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
<script type="text/javascript">
function validateform() {
	var newpass = $('#newpassword').val();
	var confirmpass = $('#confirmpassword').val();
	if(newpass != confirmpass) {
		$('#passerror').show();
		return false;
	}
}
</script>
