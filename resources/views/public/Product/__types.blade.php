@include('header')

    
<section id="breadcrumbs" class="breadcrumbs">
  <div class="container">
	<ol>
		<li><a href="{{ url('/') }}">Home</a></li>
		@if($parentlevel > 0)
		<li><a href="{{ url('/category/'.$parenturl) }}">{{ $parentname }}</a></li>
		@endif
		<li><div class="pageheading">{{ $categoryname }}</div></li>
	</ol>
  </div>
</section>

<!-- Category Area -->
        <section class="category">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="category-box">
                            <div class="sec-title">
                                <h6>Categories</h6>
                            </div>
                            <!-- accordion -->
                            <div id="accordion">
								@if($categories)
									@foreach($categories as $category)
										<div class="card">
											@php $subcategories = []; @endphp
											@php $subcategories = \App\Models\Category::where('ParentLevel', '=', $category->TypeId)->where('TypeStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get(); @endphp
											@if(count($subcategories) > 0)	
												<div class="card-header">
													<a href="{{ url('/category/'.$category->UniqueKey) }}" data-toggle="collapse" data-target="#collapse{{ $category->TypeId }}">
														<span>{{ $category->EnName }}</span>
														<i class="fa fa-angle-down"></i>
													</a>
												</div>
												<div id="collapse{{ $category->TypeId }}" class="collapse">
													<div class="card-body">
														<ul class="list-unstyled">
															@foreach($subcategories as $subcategory)
																<li><a href="{{ url('/category/'.$subcategory->UniqueKey) }}"><i class="fa fa-angle-right"></i> {{ $subcategory->EnName }}</a></li>
															@endforeach
														</ul>
													</div>
												</div>
											@else
												<div class="card-header">
													<a href="{{ url('/category/'.$category->UniqueKey) }}">
														<span>{{ $category->EnName }}</span>													
													</a>
												</div>
											@endif
										</div>
									@endforeach
                                @endif
                            </div>
                        </div>
                        <div class="cat-brand">
                            <div class="sec-title">
                                <h6>Brands</h6>
                            </div>
                            <div class="brand-box">
                                <ul class="list-unstyled">
								@if($brands)
									@foreach($brands as $brand)
                                    <li><input type="checkbox" id="" value="{{ $brand->BrandId }}" name="brands[]"><label for="{{ $brand->EnName }}">{{ $brand->EnName }}</label></li>
                                    @endforeach
								@endif
                                </ul><div class="view-all"><a href="">View All Brands</a></div>
                            </div>
                        </div>
                       
                        
                        <div class="pro-tag">
                            <div class="sec-title">
                                <h6>Product Tag</h6>
                            </div>
                            <div class="tag-box">
                                <a href="">Lights</a>
                                <a href="">Tools</a>
                                <a href="">Garden</a>
                                <a href="">Equipment</a>
                                <a href="">Stationary</a>
                                <a href="">Building Supplies</a>
                                <a href="">Household Essentials</a>
                                <a href="">Electrical</a>
                                <a href="">Oil & Gas</a>
                            </div>
                        </div>
                        <div class="add-box">
                            <a href=""><img src="images/s-banner2.jpg" alt="" class="img-fluid"></a>
                        </div>
                    </div>
					
                    <div class="col-md-9">
                        <div class="product-box">
                            <div class="cat-box d-flex justify-content-between">
                                <!-- Nav tabs -->
                                <div class="view">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#grid"><i class="fa fa-th-large"></i></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#list"><i class="fa fa-th-list"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="sortby">
                                    <span>Sort By</span>
                                    <select class="sort-box" id="sortby" name="sortby">
                                        <option value="DisplayOrder" @if($sortby == 'DisplayOrder') selected @endif>Position</option>
                                        <option value="EnName" @if($sortby == 'EnName') selected @endif>Name</option>
                                        <option value="StandardPrice" @if($sortby == 'StandardPrice') selected @endif>Price</option>
                                    </select>
                                </div>
                                <div class="show-item">
                                    <span>Show</span>
                                    <select class="show-box" id="showrecord" name="showrecord">
                                        <option value="9" @if($show == 9) selected @endif>9</option>
                                        <option value="15" @if($show == 15) selected @endif>15</option>
                                        <option value="24" @if($show == 24) selected @endif>24</option>
                                    </select>
                                </div>
								
                                <div class="page">
									@if($types->total() == 0)
										<p>Page 0 of 0</p>
									@else
										<p>Page {{ $page }} of {{ $types->lastPage() }}</p>
									@endif
                                </div>
                            </div>
                            <!-- Tab panes -->
							
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="grid" role="tabpanel">
                                    <div class="row">	
										@if($types)
											@foreach($types as $type)
												@php
													$subtypes = \App\Models\Category::where('ParentLevel', '=', $type->TypeId)->where('TypeStatus', '=', '1')->count();
												@endphp
												<div class="col-lg-4 col-md-6">
													<div class="tab-item">
														<div class="tab-img">
															@if($subtypes > 0)
																<a href="{{ url('/type/'.$type->UniqueKey) }}">
															@else
																<a href="{{ url('/category/'.$type->UniqueKey) }}">
															@endif
															@if($type->Image != '')
																<img class="main-img img-fluid" src="{{ url('/uploads/category/'.$type->Image) }}" style="height:246px;" alt="{{ $type->EnName }}">
																<img class="sec-img img-fluid" src="{{ url('/uploads/category/'.$type->Image) }}" style="height:246px;" alt="{{ $type->EnName }}">
															@else
																<img class="main-img img-fluid" src="{{ url('/images/noimage.png') }}" alt="{{ $type->EnName }}">
																<img class="sec-img img-fluid" src="{{ url('/images/noimage.png') }}" alt="{{ $type->EnName }}">
															@endif
															</a>
														</div>
														<div class="tab-heading">
															<p><a href="">{{ $type->EnName }}</a></p>
														</div>	
													</div>	
												</div>
											@endforeach
										@endif
                                    </div>
                                </div>
                                
                            </div>
							
							{{ $types->appends($_GET)->links() }}
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Category Area -->


@include('footer')
    </body>
</html>
<script type="text/javascript">
$(document).ready(function() {
	$('#sortby').change(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		if(sortby != '' && showrecord != '') {
			window.location = "{{ url('/category/'.$urlkey) }}"+"?sortby="+sortby+"&show="+showrecord;
		}
	});
	$('#showrecord').change(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		if(sortby != '' && showrecord != '') {
			window.location = "{{ url('/category/'.$urlkey) }}"+"?sortby="+sortby+"&show="+showrecord;
		}
	});
});

</script>