@include('header')

    
<section id="breadcrumbs" class="breadcrumbs">
  <div class="container">
	<ol>
		<li><a href="{{ url('/') }}">Home</a></li>
		@if($grandparentname != '')
		<li><a href="{{ url('/category/'.$grandparenturl) }}">{{ $grandparentname }}</a></li>
		@endif
		@if($parentlevel > 0)
		<li><a href="{{ url('/type/'.$parenturl) }}">{{ $parentname }}</a></li>
		@endif
		<li><div class="pageheading">{{ $categoryname }}</div></li>
	</ol>
  </div>
</section>
@php 
	$currenturl = \Request::url(); 	
@endphp


<!-- Category Area -->
        <section class="category">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        @if($products->total() > 0)
							<div class="cat-brand">
								<div class="sec-title">
									<h6>Brands</h6>
								</div>
								<div class="brand-box">
									<ul class="list-unstyled">
									@php $brandids = [0]; @endphp
									@if($bid)
										@php $brandids = @explode(',', $bid); @endphp
									@endif	
									@if($brands)
										@foreach($brands as $brand)
										 <li><input type="checkbox" id="brand{{ $brand->BrandId }}" value="{{ $brand->BrandId }}" name="brands[]" class="brands" data-value="{{ $brand->BrandId }}" onchange="setbrand(this.value);" @if(in_array($brand->BrandId, $brandids)) checked @endif><label for="{{ $brand->EnName }}">{{ $brand->EnName }}</label></li>
										@endforeach
									@endif
									</ul>
								</div>
							</div>
                        
                        
							@if(count($producttags) > 0)
							<div class="pro-tag">
								<div class="sec-title">
									<h6>Product Tag</h6>
								</div>
								<div class="tag-box">
									@foreach($producttags as $producttag)
										@if($producttag->ProdTags != '')
											<a href="javascript:void(0);">{{ $producttag->ProdTags }}</a>
										@endif
									@endforeach                                
								</div>
							</div>
							@endif
						@endif
                        
						@if($adbanners)
							@php 
								$x = 0;
							@endphp
							@foreach($adbanners as $adbanner)
								<div class="add-box" @if($x > 0) style="margin-top:10px;" @endif>
									@if($adbanner->Video != '')
										{!! $adbanner->Video !!}
									@else
										<a href="{{ $adbanner->ban_link }}"><img src="{{ url('/uploads/bannerads/'.$adbanner->	EnBanimage)}}" alt="{{ $adbanner->ban_name }}" class="img-fluid"></a>
									@endif
								</div>
								@php
									++$x;
								@endphp
							@endforeach
						@endif
						
						
                    </div>
					
                    <div class="col-md-9">
                        <div class="product-box">
                            <div class="cat-box d-flex justify-content-between">
                                <!-- Nav tabs -->
                                <!--div class="view">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#grid"><i class="fa fa-th-large"></i></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#list"><i class="fa fa-th-list"></i></a>
                                        </li>
                                    </ul>
                                </div-->
                                <div class="sortby">
                                    <span>Sort By</span>
                                    <select class="sort-box" id="sortby" name="sortby">
                                        <option value="DisplayOrder" @if($sortby == 'DisplayOrder') selected @endif>Position</option>
                                        <option value="EnName" @if($sortby == 'EnName') selected @endif>Name</option>
                                        <option value="StandardPrice" @if($sortby == 'StandardPrice') selected @endif>Price</option>
                                        <option>Rating</option>
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
								
                                <div class="page"  style="display:none;">
									@if($products->total() == 0)
										<p>Page 0 of 0</p>
									@else
										<p>Page {{ $page }} of {{ $products->lastPage() }}</p>
									@endif
                                </div>
                            </div>
                            <!-- Tab panes -->
							@if($products->total() == 0)
								<div class="" align="center">No Products Found!</div>
							@endif	
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="grid" role="tabpanel">
                                    <div class="row" id="productslist">	
										@if($products)
											@foreach($products as $product)
												@php
													$options = 0;
												@endphp	
												<div class="col-lg-3 col-md-6 col-6">
													<div class="tab-item">
														<div class="tab-img">
															@if($product->Quantity <= 0)
																<div class="box-no-product"><span class="boxAdjest">Out of Stock</span></div>
															@endif	
															<div class="box-Added-product" id="success{{ $product->Id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Cart</span></div>
															<div class="box-Added-product" id="setfav{{ $product->Id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Favourite List</span></div>
															<div class="box-no-product" id="setfaverror{{ $product->Id }}" style="display:none;"><span class="boxAdjest">Please Login to Add Favourite</span></div>
															<div class="img-box">
															<a href="{{ url('/prod/'.$product->UniqueKey) }}">
															@if($product->Image != '')
																<img class="main-img img-fluid" src="{{ url('/uploads/product/'.$product->Image) }}" alt="{{ $product->EnName }}">
																<img class="sec-img img-fluid" src="{{ url('/uploads/product/'.$product->Image) }}" alt="{{ $product->EnName }}">
															@else
																<img class="main-img img-fluid" src="{{ url('/images/noimage.png') }}" alt="{{ $product->EnName }}">
															@endif
															</a>
															</div>
															@if(in_array($product->Id, $favproducts))
																<div class="heartIcon"><a href="#"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px; color: #999; font-size: 21px;"></i></a></div>
															@else
																<div class="layer-box" id="heart{{ $product->Id }}">
																<a href="javascript:void(0);" class="it-fav" data-toggle="tooltip" data-placement="left" title="Favourite" onclick="setfavorite('{{ $product->Id }}')"><img src="{{ url('/images/it-fav.png') }}" alt=""></a>
																</div>
															@endif
															
														</div>
														<div class="tab-heading">
															<p><a href="{{ url('/prod/'.$product->UniqueKey) }}" alt="{{ $product->EnName }}" title="{{ $product->EnName }}">@if(strlen($product->EnName) > 30){{ substr($product->EnName, 0, 30) }}...@else {{ $product->EnName }} @endif</a></p>
														</div>
														<div class="img-content d-flex justify-content-between">
															<div class="wid_full">
																
																<ul class="list-unstyled list-inline price">
																	@php	
																		$displayprice = $product->Price;
																		$price = new \App\Models\Price();
																		//$displayprice = $price->getPrice($product->Id);
																		$actualprice = $price->getGroupPrice($product->Id);
																		$displayprice = $price->getDiscountPrice($product->Id);
																		$installmentPrice = $price->getInstallmentPrice($displayprice);
																	@endphp
																	
																	@if($displayprice < $actualprice)		
																	<li class="list-inline-item">
																		${{ number_format($displayprice, 2) }}
																	</li>																	
																	<li class="list-inline-item strikeoutprice">${{ number_format($actualprice, 2) }}</li>
																	@else
																		<li class="list-inline-item text-center">
																			${{ number_format($displayprice, 2) }}
																		</li>
																	@endif
																</ul>
																<div class="text-center mt-3">
																	or installments of ${{ number_format($installmentPrice, 2) }} with <img src="{{ url('/images/8dc4dab.png') }}" style="width:46px; vertical-align: inherit;">
																</div>
																<div class="text-center mt-3">
																@if($product->Quantity > 0)
																	@php																	
																		$options = \App\Models\ProductOptions::where('Prod', '=', $product->Id)->count();
																	@endphp
																	 
																	@if($options > 0)
																		<a href="{{ url('/prod/'.$product->UniqueKey) }}" class="site-btn bg-dark mx-auto textyellow">More Details</a>
																	@else
																		<a href="javascript:void(0);" onclick="addtocart('{{ $product->Id }}');" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a>
																	@endif
																@else
																	<a href="javascript:void(0);" class="site-btn bg-dark mx-auto textyellow">Out of Stock</a>
																@endif
																</div>
															</div>
															
														</div>
													</div>
												</div>
											@endforeach
										@endif
                                    </div>
                                </div>
                                
                            </div>
							
							<div class="pagination-box" style="float:right;">
								@if ($products->hasPages() && 1==2)
									<ul class="list-unstyled list-inline text-center">
										{{-- Previous Page Link --}}
										@if ($products->onFirstPage())
											<li class="disabled list-inline-item"><span><<</span></li>
										@else
											<li class="list-inline-item"><a href="{{ $products->appends($_GET)->previousPageUrl() }}" rel="prev"><<</a></li>
										@endif

										@if($products->currentPage() > 3)
											<li class="hidden-xs list-inline-item"><a href="{{ $products->appends($_GET)->url(1) }}">1</a></li>
										@endif
										@if($products->currentPage() > 4)
											<li class="list-inline-item"><span>...</span></li>
										@endif
										@foreach(range(1, $products->lastPage()) as $i)
											@if($i >= $products->currentPage() - 2 && $i <= $products->currentPage() + 2)
												@if ($i == $products->currentPage())
													<li class="active list-inline-item"><a href="">{{ $i }}</a></li>
												@else
													<li class="list-inline-item"><a href="{{ $products->appends($_GET)->url($i) }}">{{ $i }}</a></li>
												@endif
											@endif
										@endforeach
										@if($products->currentPage() < $products->lastPage() - 3)
											<li class="list-inline-item"><span>...</span></li>
										@endif
										@if($products->currentPage() < $products->lastPage() - 2)
											<li class="hidden-xs list-inline-item"><a href="{{ $products->appends($_GET)->url($products->lastPage()) }}">{{ $products->lastPage() }}</a></li>
										@endif

										{{-- Next Page Link --}}
										@if ($products->hasMorePages())
											<li class="list-inline-item"><a href="{{ $products->appends($_GET)->nextPageUrl() }}" rel="next">>></a></li>
										@else
											<li class="disabled  list-inline-item"><span>>></span></li>
										@endif
									</ul>
								@endif
								
								@if($products->total() > 20)
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
	$('#loadmore').click(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		var cid = "{{ $categoryid }}";
		var bid = "{{ $bid }}";
		var pagenum = $('#pagenum').val();
		pagenum = parseInt(pagenum) + 1;
		$('#pagenum').val(pagenum);
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/getproductslist',				
			data: {'page': pagenum, 'IsPromotion': '0', 'cid': cid, 'bid': bid, 'sortby': sortby, 'show': showrecord, 'searchkey': ''},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {
				
				if(response != '') {
					$('#productslist').append(response);
				} else {
					$('#loadmore').hide();
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});
	});	
});

function setbrand(brandid) {
	var bid = "{{ $bid }}";
	var isChecked = $("#brand"+brandid).is(":checked");
	if(isChecked) {
		if(bid != '') {
			bid = bid + "," + brandid;
		} else {
			bid = brandid;
		}
	} else {
		var bids = bid.split(',');
		bids = $.grep(bids, function(value) {
		  return value != brandid;
		});
		bid = bids.join(',');
	}
	var sortby = $('#sortby').val();
	var showrecord = $('#showrecord').val();
	window.location = "{{ url('/category/'.$urlkey) }}"+"?sortby="+sortby+"&show="+showrecord+"&bid="+bid;
}

function addtocart(ProdId) {
	var redirecturl = '{{ $currenturl }}';
	var token = "{!! csrf_token() !!}";
	$.ajax({
		type: "POST",
		url: '{{ url("/") }}/addtocart',				
		data: {'prodid': ProdId},
		headers: {'X-CSRF-TOKEN': token},		
		success: function(response) {
			console.log(response);
			if(response > 0) {
				$('#cartcount').html(response);
				$('#mcartcount').html(response);
				$('#success'+ProdId).show();
				$('.box-Added-product').delay(5000).fadeOut('fast');
				//window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=success";
			} else {
				//window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=failed";
			}
		},error: function(ts) {				
			console.log("Error:"+ts.responseText);  
		}
	});
}

function setfavorite(ProdId) {
	var token = "{!! csrf_token() !!}";
	$.ajax({
		type: "POST",
		url: '{{ url("/") }}/addtofavorite',				
		data: {'prodid': ProdId},
		headers: {'X-CSRF-TOKEN': token},		
		success: function(response) {
			console.log(response);
			if(response == 'Success') {				
				$('#setfav'+ProdId).show();
				$('.box-Added-product').delay(5000).fadeOut('fast');
				$('#heart'+ProdId).html('<div class="heartIcon"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px; color: #999; font-size: 21px;"></i></div>');	
			} else {
				$('#setfaverror'+ProdId).show();
				$('.box-no-product').delay(5000).fadeOut('fast');	
			}
		},error: function(ts) {				
			console.log("Error:"+ts.responseText);  
		}
	});
}

</script>