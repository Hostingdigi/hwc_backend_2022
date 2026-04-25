@include('admin.includes.header')
<style>
.ck-editor__editable {
    min-height: 500px;
}
</style>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<body class="horizontal-layout horizontal-menu 2-columns  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.mainmenu')
    <!-- BEGIN: Header-->
    <div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Order</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/orders/') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                    <h4 class="card-title">Update</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="addproducttag" id="addproducttag" method = "post" action="{{ url('/admin/orders/updateselfcollect') }}" enctype="multipart/form-data"> 
                                        <input type="hidden" name="orderid" value="{{ $order->order_id}}">			
										
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Invoice No <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
																@php
																	$orderid = $order->order_id;
																	if(strlen($orderid) == 3) {
																		$orderid = date('Ymd', strtotime($order->created_at)).'0'.$orderid;
																	} elseif(strlen($orderid) == 2) {
																		$orderid = date('Ymd', strtotime($order->created_at)).'00'.$orderid;
																	} elseif(strlen($orderid) == 1) {
																		$orderid = date('Ymd', strtotime($order->created_at)).'000'.$orderid;
																	} else {
																		$orderid = date('Ymd', strtotime($order->created_at)).$orderid;
																	}
																@endphp
                                                                <input type="text" id="invoiceno" class="form-control" name="invoiceno" value = "{{ $orderid }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>   
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Status</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select name="order_status" id="order_status" class="form-control">
                                                                <option value="1">Pending</option>
                                                                <option value="9">Ready For Collection</option>
                                                                <option value="3">Collected / Shipped</option>
                                                                </select>    
                                                             </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Collection Start <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" id="colection_start_date" class="form-control pickadate" name="colection_start_date" value = "{{ date('d F, Y') }}" >
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" id="colection_start_time" class="form-control pickatime" name="colection_start_time" value = "" >
                                                            </div>
                                                        </div>
                                                    </div>  
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Collection End <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" id="colection_to_date" class="form-control pickadate" name="colection_to_date" value = "{{ date('d F, Y', strtotime('+5 days')) }}" >
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" id="colection_to_time" class="form-control pickatime" name="colection_to_time" value = "" >
                                                            </div>
                                                        </div>
                                                    </div>  
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Remarks <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea id="remarks" class="form-control" name="remarks" value = "{{ $order->remarks }}" required></textarea>
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

<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
<script>

    $('.pickadate').pickadate();

     $('.pickatime').pickatime();

</script>