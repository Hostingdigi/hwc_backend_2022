@include('admin.includes.header')
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
                            <h2 class="content-header-title float-left mb-0">Category Shipping Charge</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/category/shipping-charges') }}" class="btn " tabindex="0" aria-controls="banner_master">
              <span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
						</div>
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <section id="basic-horizontal-layouts">
                    <div class="row match-height">
						<div class="col-sm-12">
                            @if(Session::get('message'))
                            <div class="alert alert-danger">{!! Session::get('message') !!}</div>
                            @endif
                        </div>
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                    <form class="form form-horizontal"  method="post" action="{{ url('/admin/category/shipping-charges/update') }}">                                            
                                            {{ csrf_field() }}
                                            <input type="hidden" name="group_id" value="{{$group->id}}">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Group Name <span style="color:red;font-size: 16px;">*</span> </span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" value="{{$group->name}}" id="gname" class="form-control" required name="gname" placeholder="Enter the group name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Amount(SGD) <span style="color:red;font-size: 16px;">*</span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="number" value="{{$group->amount}}" class="form-control" required name="amount" placeholder="Enter the amount">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Categories <span style="color:red;font-size: 16px;">*</span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                @php
                                                                    $currentGroups = explode(',',$group->category_ids);
                                                                @endphp
                                                                <select class="form-control" id="ParentLevel" required name="categories[]" multiple="multiple" placeholder="asdasd">
																	@foreach($categories as $category)
                                                                    <optgroup label="{{ $category->EnName }}">
																		@php
																		$subcats = \App\Models\Category::where(['ParentLevel' => $category->TypeId, 'TypeStatus' => 1])
                                                                            ->whereNotIn('TypeId',$availableCategories)
                                                                            ->orderBy('EnName')->get();
																		@endphp
																			@foreach($subcats as $subcat)
																				@php
																				$subsubcats = \App\Models\Category::where(['ParentLevel' => $subcat->TypeId, 'TypeStatus' => 1])
                                                                                    ->whereNotIn('TypeId',$availableCategories)
                                                                                    ->orderBy('EnName')->get();
																				@endphp
																				@if($subsubcats->isNotEmpty())
                                                                                    <optgroup label="{{ $subcat->EnName }}">
																					@foreach($subsubcats as $subsubcat)
																					<option @if(in_array($subsubcat->TypeId,$currentGroups)) selected="selected" @endif value="{{ $subsubcat->TypeId }}">{{ $subsubcat->EnName }}</option>
																					@endforeach
                                                                                    </optgroup>
                                                                                @else
																				<option @if(in_array($subcat->TypeId,$currentGroups)) selected="selected" @endif value="{{ $subcat->TypeId }}">{{ $subcat->EnName }}</option>
																				@endif
																			@endforeach
                                                                        </optgroup>
																	@endforeach
																</select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1" name="submit_action">Update</button>
                                                        <a href="{{url('admin/category/shipping-charges')}}" class="btn btn-outline-warning mr-1 mb-1">Cancel</a>
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
<script>
    $(document).ready(function(){
        $("#ParentLevel").select2({
            placeholder: "Select a category",
            allowClear: true
        });
    });
</script>