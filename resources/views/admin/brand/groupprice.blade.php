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



                        <div class="col-8">



                            <h2 class="content-header-title float-left mb-0">Manage Group Price</h2>                            



                        </div>



						<div class="col-4" style="float:right; text-align:right;">



							<div class="dt-buttons btn-group"><a href="{{ url('/admin/brands') }}" class="btn " tabindex="0" aria-controls="Products">



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

										<form class="form form-horizontal" name="products_frm" id="products_frm" method = "post" @if($editprice) action="{{ url('/admin/brands/updategroupprice') }}" @else action="{{ url('/admin/brands/addgroupprice') }}" @endif enctype="multipart/form-data">
										<input type="hidden" name="brandid" value="{{ $brand->BrandId }}">			           
										<input type="hidden" name="editid" @if($editprice) value="{{ $editprice->Id }}" @endif>

										{{ csrf_field() }}	

										<div class="form-body">

                                                <div class="row">
                                                    <div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">
                                                                <span>Brand Name </span>
                                                            </div>
                                                            <div class="col-md-8">

                                                                {{ $brand->EnName }}

                                                            </div>
                                                        </div>
                                                    </div>

													
													
													<div class="col-12">

                                                        <div class="form-group row">

                                                            <div class="col-md-4">

                                                                <span>Group</span>

                                                            </div>

                                                            <div class="col-md-6">

																<select name="GroupId" class="form-control" required>              

																	   <option value="0" selected="">Select</option>					
																		@if(@groups)
																			@foreach($groups as $group)
																			<option value="{{ $group->Id }}" @if($editprice) @if($editprice->GroupId == $group->Id) selected @endif @endif>{{ $group->GroupTitle }}</option>
																			@endforeach
																		@endif	
																	                

																</select>

																	

                                                            </div>

                                                        </div>

                                                    </div>
													
													<div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Price (%)</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <input type="text" id="Price" required class="form-control" name="Price" placeholder="Price" @if($editprice) value="{{ $editprice->Price }}" @endif>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <select name="type" class="form-control" id = "type">							
                                                                    <option value="1" @if($editprice) @if($editprice->type == 1) selected @endif @endif>Up </option>							
                                                                    <option value="2" @if($editprice) @if($editprice->type == 2) selected @endif @endif>Discount</option>							
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

                                                            <div class="col-md-6">
                                                            <select name="Status" class="form-control">
																<option value="1" @if($editprice) @if($editprice->Status == 1) selected @endif @endif>Active</option>
																<option value="0" @if($editprice) @if($editprice->Status == 0) selected @endif @endif>In-Active</option>
															</select>
															</div>
                                                        </div>
                                                    </div>

													<div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
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
								
								<th>Group</th>

								<th>Price</th>
								<th>Status</th>	
								
								<th>Actions</th>

								  </tr>

                            </thead>

                            <tbody>						

						 	 	@if($groupprices)
									@foreach($groupprices as $groupprice)
									@php
										$groupname = '';
										$group = \App\Models\CustomerGroup::where('Id', '=', $groupprice->GroupId)->first();
										if($group) {
											$groupname = $group->GroupTitle;
										}
									@endphp
									<tr>
										<td>{{ $groupname }}</td>
										<td>@if($groupprice->type == 1) Up @else Discount @endif - {{ $groupprice->Price }}%</td>
										<td>@if($groupprice->Status == 1) Active @else In-Active @endif</th>
										<td><a href="{{ url('/admin/brands/groupprice/'.$groupprice->Id.'/'.$groupprice->Brand.'/editgroupprice') }}">
										<i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="chkdelete({{ $groupprice->Id }}, {{ $groupprice->Brand }})"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a></td>
									</tr>
									@endforeach
								@endif

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
$('.dataex-html5-selectors').DataTable( {	dom: 'Bfrtip',	buttons: [			]});
function chkdelete(id, brandid) {
	var chk = confirm('Are you sure! you want to delete this brand group price?');
	if(chk) {
		window.location = "{{ url('/admin/brands/groupprice') }}/"+id+"/"+brandid+"/destroygroupprice";	
	}
}

</script>