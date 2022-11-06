@php
	$categories = $brands = [];
	$categories = \App\Models\Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->get();
	$brands = \App\Models\Brand::where('BrandStatus', '=', '1')->where('ParentLevel', '=', '0')->where('PopularBrand', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
@endphp
<div class="sidenav" id="by_category">
	<div class="m-menu">Main Menu</div>
		<a href="javascript:void(0)" class="closebtn" id="close_nav">&times;</a>
		<div id="category_menu">
			@if(count($categories) > 0)
				<ul class="sub-menu" >
				@foreach($categories as $category)
					@php
						$subcategories = [];
					@endphp
					@php
						$subcategories = \App\Models\Category::where('ParentLevel', '=', $category->TypeId)->where('TypeStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
					@endphp
					@if(count($subcategories) > 0)
						<li><a href="{{ url('/'.$category->UniqueKey) }}">{{ $category->EnName }}<i class="fa fa-caret-right" aria-hidden="true" style="color:#ffff00"></i></a>
							<ul>
								@foreach($subcategories as $subcategory)
									@php
										$subsubcategories = [];
									@endphp
									@php
										$subsubcategories = \App\Models\Category::where('ParentLevel', '=', $subcategory->TypeId)->where('TypeStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get(); 
									@endphp
									@if(count($subsubcategories) > 0)
										<li><a href="{{ url('/'.$subcategory->UniqueKey) }}">{{ $subcategory->EnName }}<i class="fa fa-caret-right" aria-hidden="true" style="color:#ffff00"></i></a>
											<ul>
											@foreach($subsubcategories as $subsubcategory)
												<li><a href="{{ url('/category/'.$subsubcategory->UniqueKey) }}">{{ $subsubcategory->EnName }}</a></li>@endforeach
											</ul>
										</li>
									@else
										<li><a href="{{ url('/category/'.$subcategory->UniqueKey) }}">{{ $subcategory->EnName }}</a></a></li>@endif
									</li>
								@endforeach
							</ul>
						</li>
						@else
							<li><a href="{{ url('/category/'.$category->UniqueKey) }}">{{ $category->EnName }}</a></li>
						@endif
					@endforeach
					</ul>
				@endif
				</div>
				</div>
				<div class="sidenav" id="by_brand">	<a href="javascript:void(0)" class="closebtn" id="close_nav">&times;</a>	<div id="brand_menu">	
				@if(count($brands) > 0)
					<div class="container">
						<div class="row"><div class="col-md-12"><h1>Featured Brands</h1></div></div>
						<div class="row">
						@foreach($brands as $brand)
							@if(file_exists(public_path('/uploads/brands/'.$brand->Image)))
								<div class="col-md-4"><a href="{{ url('/brand/'.$brand->UniqueKey) }}"><img src="{{ url('/uploads/brands/'.$brand->Image) }}" class="img-fluid" alt="{{ $brand->EnName }}"></a></div>
							@else
								<div class="col-md-4"><a href="{{ url('/brand/'.$brand->UniqueKey) }}"><img src="{{ url('/images/noimage.png') }}" class="img-fluid" alt="{{ $brand->EnName }}"></a></div>
							@endif
						@endforeach
						</div>
						<div class="row"><div class="col-md-12"><a href="{{ url('/brands') }}" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL BRANDS</a></div></div>
					</div>
				@endif
			</div>
		</div>