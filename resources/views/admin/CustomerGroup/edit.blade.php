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
                            <h2 class="content-header-title float-left mb-0">Manage Customer Group</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/customergroup') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                    <form class="form form-horizontal" name="addcountry" id="addcountry" method = "post" action="{{ url('/admin/customergroup/'.$customergroup->Id) }}" enctype="multipart/form-data"> 
                                    <input type="hidden" name="id" value="{{ $customergroup->Id }}">			
	<input type="hidden" name="_method" value="PUT">    
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Group Name <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="GroupTitle" class="form-control" name="GroupTitle" value="{{ $customergroup->GroupTitle }}" placeholder="Group Title" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price Adjustment</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <input type="text" id="DiscountValue" class="form-control" name="DiscountValue" value="{{ $customergroup->DiscountValue }}" placeholder="Discount Value">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <select name="type" class="form-control" id = "type">							
                                                                    <option value="1" @if($customergroup->type == 1) selected @endif>Up </option>							 
	                                                                <option value="2" @if($customergroup->type == 2) selected @endif>Discount</option>							
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <select name="DiscountType" class="form-control" id = "DiscountType">							
                                                                    <option value="1" @if($customergroup->DiscountType == 1) selected @endif>SGD</option>							 
	                                                                <option value="2" @if($customergroup->DiscountType == 2) selected @endif>%</option>								
                                                                </select>
                                                            </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <select name="Status" class="form-control" id = "Status">						
                                                                <option value="0" @if($customergroup->Status == 0) selected @endif>In-Active</option>							
	                                                            <option value="1" @if($customergroup->Status == 1) selected @endif>Active</option>
		                                                    </select>
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