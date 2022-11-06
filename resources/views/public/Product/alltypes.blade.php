@include('header')

    
<section id="breadcrumbs" class="breadcrumbs">
  <div class="container">
	<ol>
		<li><a href="{{ url('/') }}">Home</a></li>
		
		<li><div class="pageheading">Types</div></li>
	</ol>
  </div>
</section>

<!-- Category Area -->
        <section class="category">
            <div class="container">
                <div class="row">
                    
					
                    <div class="col-md-12">
                        <div class="product-box">
                            <div class="cat-box d-flex justify-content-between">
                                <!-- Nav tabs -->
                                
                                <div class="sortby">
                                    <span>Sort By</span>
                                    <select class="sort-box" id="sortby" name="sortby">
                                        <option value="DisplayOrder" @if($sortby == 'DisplayOrder') selected @endif>Position</option>
                                        <option value="EnName" @if($sortby == 'EnName') selected @endif>Name</option>                                        
                                    </select>
                                </div>
                                <div class="show-item">
                                    <span>Show</span>
                                    <select class="show-box" id="showrecord" name="showrecord">
                                        <option value="20" @if($show == 20) selected @endif>20</option>
                                        <option value="28" @if($show == 28) selected @endif>28</option>
                                        <option value="36" @if($show == 36) selected @endif>36</option>
                                    </select>
                                </div>
								
                                <div class="page" style="display:none;">
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
                                    <div class="row" id="typeslist">	
										@if($types)
											@foreach($types as $type)
												@php
													$productscount = 0;
													$catproducts = \App\Models\Product::where('Types', '=', $type->TypeId)->where('ProdStatus', '=', '1')->count();
													$productscount = $productscount + $catproducts;
													
													$subtypes = \App\Models\Category::where('ParentLevel', '=', $type->TypeId)->where('TypeStatus', '=', '1')->get();
												@endphp	
												@if(count($subtypes) > 0)
													@foreach($subtypes as $subtype)
														@php 
															$subcatproducts = \App\Models\Product::where('Types', '=', $subtype->TypeId)->where('ProdStatus', '=', '1')->count();
															$productscount = $productscount + $subcatproducts;
															$subsubcats = [];
															$subsubcats = \App\Models\Category::where('ParentLevel','=',$subtype->TypeId)->orderBy('EnName', 'ASC')->get();
														@endphp	
														@if(count($subsubcats) > 0)
															@foreach($subsubcats as $subsubcat)
																@php 
																	$subsubcatproducts = \App\Models\Product::where('Types', '=', $subsubcat->TypeId)->where('ProdStatus', '=', '1')->count();
																	$productscount = $productscount + $subsubcatproducts;
																@endphp
															@endforeach
														@endif	
													@endforeach
												@endif
												
												<div class="col-lg-3 col-md-6">
													<div class="tab-item">
														<div class="tab-img">
															@if(count($subtypes) > 0)
																<a href="{{ url('/type/'.$type->UniqueKey) }}">
															@else
																<a href="{{ url('/category/'.$type->UniqueKey) }}">
															@endif
															@if($type->Image != '' && file_exists(public_path('/uploads/category/'.$type->Image)))
																<img class="main-img img-fluid" src="{{ url('/uploads/category/'.$type->Image) }}"  alt="{{ $type->EnName }}">
																<img class="sec-img img-fluid" src="{{ url('/uploads/category/'.$type->Image) }}"  alt="{{ $type->EnName }}">
															@else
																<img class="main-img img-fluid" src="{{ url('/images/noimage.png') }}" alt="{{ $type->EnName }}" >
															@endif
															</a>
														</div>
														<div class="caption">	
															@if(count($subtypes) > 0)
																<a href="{{ url('/type/'.$type->UniqueKey) }}">
															@else
																<a href="{{ url('/category/'.$type->UniqueKey) }}">
															@endif
															<h4 class="pt-3 pb-1">{{ $type->EnName }}</h4>
															</a>
															<p>{{ $productscount }} Products</p>
														</div>	
													</div>	
												</div>
											@endforeach
										@endif
                                    </div>
                                </div>
                                
                            </div>
							
							<div class="pagination-box" style="float:right;">
								@if ($types->hasPages() && 1==2)
									<ul class="list-unstyled list-inline text-center">
										{{-- Previous Page Link --}}
										@if ($types->onFirstPage())
											<li class="disabled list-inline-item"><span><<</span></li>
										@else
											<li class="list-inline-item"><a href="{{ $types->appends($_GET)->previousPageUrl() }}" rel="prev"><<</a></li>
										@endif

										@if($types->currentPage() > 3)
											<li class="hidden-xs list-inline-item"><a href="{{ $types->appends($_GET)->url(1) }}">1</a></li>
										@endif
										@if($types->currentPage() > 4)
											<li class="list-inline-item"><span>...</span></li>
										@endif
										@foreach(range(1, $types->lastPage()) as $i)
											@if($i >= $types->currentPage() - 2 && $i <= $types->currentPage() + 2)
												@if ($i == $types->currentPage())
													<li class="active list-inline-item"><a href="">{{ $i }}</a></li>
												@else
													<li class="list-inline-item"><a href="{{ $types->appends($_GET)->url($i) }}">{{ $i }}</a></li>
												@endif
											@endif
										@endforeach
										@if($types->currentPage() < $types->lastPage() - 3)
											<li class="list-inline-item"><span>...</span></li>
										@endif
										@if($types->currentPage() < $types->lastPage() - 2)
											<li class="hidden-xs list-inline-item"><a href="{{ $types->appends($_GET)->url($types->lastPage()) }}">{{ $types->lastPage() }}</a></li>
										@endif

										{{-- Next Page Link --}}
										@if ($types->hasMorePages())
											<li class="list-inline-item"><a href="{{ $types->appends($_GET)->nextPageUrl() }}" rel="next">>></a></li>
										@else
											<li class="disabled  list-inline-item"><span>>></span></li>
										@endif
									</ul>
								@endif
								@if($types->total() > 20)
									<a href="javascript:void(0);" id="loadmore" class="loadmorebtn mx-auto">Load More</a>
								@endif
								<input type="hidden" id="pagenum" value="1">
							</div>
                            
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
			window.location = "{{ url('/types/') }}"+"?sortby="+sortby+"&show="+showrecord;
		}
	});
	$('#showrecord').change(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		if(sortby != '' && showrecord != '') {
			window.location = "{{ url('/types/') }}"+"?sortby="+sortby+"&show="+showrecord;
		}
	});
	
	$('#loadmore').click(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		
		var pagenum = $('#pagenum').val();
		pagenum = parseInt(pagenum) + 1;
		$('#pagenum').val(pagenum);
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/gettypeslist',				
			data: {'page': pagenum, 'sortby': sortby, 'show': showrecord},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {
				//console.log(response);
				if(response != '') {
					$('#typeslist').append(response);
				} else {
					$('#loadmore').hide();
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});
	});
});

</script>