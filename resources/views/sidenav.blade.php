<div class="sidenav" id="by_category">
	<div class="m-menu">Main Menu</div>
		<a href="javascript:void(0)" class="closebtn" id="close_nav">&times;</a>
		<div id="category_menu">
			@if($topBar['categories']->isNotEmpty())
				<ul class="sub-menu" >
				@foreach($topBar['categories'] as $category)
					@if($category['subcategories']->isNotEmpty())
						<li><a href="{{ url($category['unique_key']) }}">{{ $category['name'] }}<i class="fa fa-caret-right" aria-hidden="true" style="color:#ffff00"></i></a>
							<ul>
								@foreach($category['subcategories'] as $subcategory)
									@if($subcategory['subcategories']->isNotEmpty())
										<li><a href="{{ url($subcategory['unique_key']) }}">{{ $subcategory['name'] }}<i class="fa fa-caret-right" aria-hidden="true" style="color:#ffff00"></i></a>
											<ul>
											@foreach($subcategory['subcategories'] as $subsubcategory)
												<li><a href="{{ url('category/'.$subsubcategory['unique_key']) }}">{{ $subsubcategory['name'] }}</a></li>@endforeach
											</ul>
										</li>
									@else
										<li><a href="{{ url('category/'.$subcategory['unique_key']) }}">{{ $subcategory['name'] }}</a></a></li>@endif
									</li>
								@endforeach
							</ul>
						</li>
						@else
							<li><a href="{{ url('category/'.$category['unique_key']) }}">{{ $category['name'] }}</a></li>
						@endif
					@endforeach
					</ul>
				@endif
				</div>
				</div>
				<div class="sidenav" id="by_brand">	<a href="javascript:void(0)" class="closebtn" id="close_nav">&times;</a>	<div id="brand_menu">	
				@if($topBar['brands']->isNotEmpty())
					<div class="container">
						<div class="row"><div class="col-md-12"><h1>Featured Brands</h1></div></div>
						<div class="row">
						@foreach($topBar['brands'] as $brand)
							@if(file_exists(public_path('/uploads/brands/'.$brand->Image)))
								<div class="col-md-4">
									<a href="{{ url('brand/'.$brand->UniqueKey) }}">
									<img src="{{ url('/uploads/brands/'.$brand->Image) }}" class="img-fluid" alt="{{ $brand->EnName }}"></a>
								</div>
							@else
								<div class="col-md-4">
									<a href="{{ url('brand/'.$brand->UniqueKey) }}">
									<img src="{{ url('/images/noimage.png') }}" class="img-fluid" alt="{{ $brand->EnName }}"></a>
								</div>
							@endif
						@endforeach
						</div>
						<div class="row"><div class="col-md-12"><a href="{{ url('brands') }}" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL BRANDS</a></div></div>
					</div>
				@endif
			</div>
		</div>