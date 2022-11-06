@include('admin.includes.header')
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:70%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title text-center">Manage Social Media</h2>                            
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
                                    <form class="form form-horizontal" name="socialmedia" id="socialmedia" method = "post" action="{{ url('/admin/socialmedia') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">                                                  
                                                    <div class="col-12">
														 <div class="form-group row">
															 <div class="col-md-2">
																
															</div>
                                                            <div class="col-md-2">
																Status
															</div>
                                                           
															<div class="col-md-6">
															Link		
															</div>
															<div class="col-md-2">
															Display Order		
															</div>
                                                        </div>
													</div>
													@foreach($socialmedias as $socialmedia)	
													<div class="col-12">
														 <div class="form-group row">
															 <div class="col-md-2">
																<span>{{ $socialmedia->name }}</span>
															</div>
                                                            <div class="col-md-2">
																<input type="checkbox" name="status_{{ $socialmedia->id }}"  value="1" @if($socialmedia->status == 1) checked @endif>
															</div>
                                                           
															<div class="col-md-6">
															<input type ="text" name="sociallink_{{ $socialmedia->id }}" value="{{ $socialmedia->sociallink }}" class="form-control">		
														  </div>
														  
															<div class="col-md-2">
															<input type ="text" name="display_order_{{ $socialmedia->id }}" value="{{ $socialmedia->display_order }}" class="form-control">		
														  </div>
                                                        </div>
													</div>													
													@endforeach
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