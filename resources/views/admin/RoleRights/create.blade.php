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
    <!-- BEGIN: Header-->
    <div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" style="width:80%; margin:0 auto;"> 
			<div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Manage Role & Rights</h2>                            
                        </div>
						<div class="col-4" style="float:right; text-align:right;">
							<div class="dt-buttons btn-group"><a href="{{ url('/admin/roleandrights') }}" class="btn " tabindex="0" aria-controls="categories"><span><i class="feather icon-arrow-left"></i> Back</span></a> </div>
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
                                    <h4 class="card-title">Add New</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" name="addannouncement" id="addannouncement" method = "post" action="{{ url('/admin/roleandrights') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-body">
                                                <div class="row">                                                 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Name <span class="badge badge-dot badge-danger"> </span></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" name="role_name" class="form-control" id="role_name">
                                                            </div>
                                                        </div>
                                                    </div>                                          
												     <div class="col-12">
                                                        <div class="form-group row">
                                                            <table class="table">
																<tr>
																	<td>Modules</td>
																	<td>Actions</td>
																</tr>
																@if($modules)
																	@foreach($modules as $module)
																		<tr>
																			<td>{{ $module->module_name }}</td>
																			@php				
																				$submodules = \App\Models\Modules::where('parent_id', '=', $module->id)->get();				
																			@endphp
																			@if($submodules)
																				<td></td></tr>
																				
																				@foreach($submodules as $submodule)
																				<tr>
																					<td style="padding-left:40px"><input type="checkbox" name="rights[]" value="{{ $submodule->id }}" onchange="checkuncheckall({{ $submodule->id }}, {{ $submodule->action_count }})" id="module_{{ $submodule->id }}">&nbsp;{{ $submodule->module_name }}</td>
																					@php
																						$x = 0;
																						$ac = 1;
																						$actions = [];
																						$actions = @explode(',',$submodule->rights);
																					@endphp
																					<td>				
																					@if($actions)
																						<table class="table"><tr>
																						@foreach($actions as $action)
																							@if($x % 5 == 0)
																								</tr><tr>
																							@endif
																							<td width="20%;"><input type="checkbox" name="rights[]" value="{{ $submodule->id.'_'.$action }}" id="{{ $submodule->id.'_'.$ac }}">&nbsp;{{ $action }}</td>
																							@php
																								++$ac;
																								++$x;
																							@endphp
																						@endforeach
																						</tr></table>
																					@endif
																					</td>
																				</tr>	
																				@endforeach
																			@else
																				</tr>	
																			@endif	
																	@endforeach
																@endif	
															</table>
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
                </section>


            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<script type="text/javascript">
function checkuncheckall(moduleid, actioncount) {
	if($('#module_'+moduleid).is(':checked')) {
		for(var i = 1; i <= actioncount; i++) {
			$('#'+moduleid+'_'+i).attr('checked','checked');
		}
	} else {
		for(var i = 1; i <= actioncount; i++) {
			$('#'+moduleid+'_'+i).removeAttr('checked');
		}
	}
}
</script>
