@include('header')

    @include('banner')
	@php 
		$currenturl = \Request::url(); 
        $roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
	@endphp
	
	<style>
	h5, h5 a { font-size: 15px; }
	</style>
	
	<!-- letest product section -->
	<section class="top-letest-product-section mt-5">
		<div class="container">
			<div class="col-12 section-title">
				<h2>Promotions</h2>
				<hr style="width:50px; border-bottom:4px solid #000;"></hr>
			</div>
			<div class="card-deck">
				<div class="row">
				@php
				@endphp
				@foreach($promoitems as $promoitem)
					@php
						$options = 0;
						$fallback = config('shop.fallback_img');
						$image = trim((string) $promoitem->Image) !== ''
							? config('shop.img_url') . '/uploads/product/' . $promoitem->Image
							: $fallback;
						$itemName = $promoitem->EnName;
					@endphp
					<div class="col-lg-2 col-md-6 col-6">
						<div class=" card">
							@if($promoitem->Quantity <= 0)
							<div class="box-no-product"><span class="boxAdjest">Out of Stock</span></div>
							@endif
							<div class="box-Added-product" id="success{{ $promoitem->Id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Cart</span></div>
							<div class="box-Added-product" id="setfav{{ $promoitem->Id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Favourite List</span></div>
							<div class="box-no-product" id="setfaverror{{ $promoitem->Id }}" style="display:none;"><span class="boxAdjest">Please Login to Add Favourite</span></div>
							<div class="img-box">
							<a href="{{ url('prod/'.$promoitem->UniqueKey) }}">
								<img src="{{ $image }}" class="card-img-top" alt="{{ $itemName }}" title="{{ $itemName }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $fallback }}';">
							</a>
							</div>
							<div class="card-body">		
								@if(auth()->check() && in_array($promoitem->Id, $favproducts))
									<div class="heartIcon"><a href="javascript:(0);"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px; color: #999; font-size: 21px;"></i></a></div>									
								@endif
								<h5 class="card-title text-center">
									<a href="{{ url('prod/'.$promoitem->UniqueKey) }}" title="{{ $itemName }}">
										{{ strlen($itemName) > 30 ? substr($itemName, 0, 30)."..." : $itemName }}
									</a>
								</h5>

								@if($promoitem->Price >0)
									<div class="row">
										@php	
											$actualprice = $promoitem->Price;
											$price = new \App\Models\Price();
											$displayprice = $price->getPrice($promoitem->Id);
											$installmentPrice = $price->getInstallmentPrice($displayprice);
											$gstprice = $roundObj->roundDecimal($price->getGSTPrice($displayprice, 'SG'));
											$actualgstprice = $roundObj->roundDecimal($price->getGSTPrice($actualprice, 'SG'));
											$installmentgstPrice = $roundObj->roundDecimal($price->getInstallmentPrice($gstprice));
										@endphp
										
										@if($gstprice < $actualgstprice)
										<div class="col-6">
											<div class="newrate">
											S${{ number_format($gstprice, 2) }}
											</div>
										</div>
										<div class="col-6">
											<div class="oldrate">S${{ number_format($actualgstprice, 2) }}</div>
										</div>
										@else
											<div class="col-12">
												<div class="newrate text-center">
												S${{ number_format($gstprice, 2) }}
												</div>
											</div>
										@endif
									</div>	
								@else
									<div class="text-center mt-3">
										<div class="prod-price-col-red">COMING SOON</div>	
									</div>
									<div class="text-center mt-3" style="margin-top:2.8rem!important;">
										Stay Tuned
									</div>						
								@endif
								<div class="text-center mt-3">
								@if($promoitem->Quantity > 0)
									@php																	
										$options = \App\Models\ProductOptions::where('Prod', '=', $promoitem->Id)->count();
									@endphp
										
									@if($options > 0 || $promoitem->Price ==0)
										<a href="{{ url('/prod/'.$promoitem->UniqueKey) }}" class="site-btn bg-dark mx-auto textyellow">More Details</a>
									@else
										<a href="javascript:(0);" onclick="addtocart('{{ $promoitem->Id }}');" id="addcart{{ $promoitem->Id }}" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a>
									@endif
								@else
									<a href="javascript:(0);" id="addcart{{ $promoitem->Id }}" class="site-btn bg-dark mx-auto textyellow">Out of Stock</a>
								@endif
								</div>
							</div>
						</div>
					</div>
				@endforeach
				</div>
			</div>	
			<div class="text-center mt-5"><a href="{{ url('/promotions') }}" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL</a></div>
		</div>
	</section>

	@if($bannerads->isNotEmpty())
	<section class="add-section">
		<div class="container my-4 mb-5">
			<!--Carousel Wrapper-->
			<div id="multi-item-example" class="carousel slide carousel-multi-item d-none d-md-block" data-ride="carousel">
				<!--Controls-->
				<div class="controls-top">
					<a class="btn-floating prevss" href="#multi-item-example" data-slide="prev"><i class="fa fa-chevron-left" style="color:#000; position:relative; left:-30px;"></i></a>
					<a class="btn-floating nextss" href="#multi-item-example" data-slide="next"><i class="fa fa-chevron-right" style="color:#000; position:relative; right:-30px;"></i></a>
				</div>
				<!--/.Controls-->
				<!--Slides-->
				<div class="carousel-inner mb-5" role="listbox">
					<div class="carousel-item active mb-5">
						<div class="row">
						@php					
							$col = 0; 
						@endphp
						@foreach($bannerads as $bannerad)
						
							@if($col % 4 == 0 && $col > 0) 
								</div></div><div class="carousel-item mb-5"><div class="row">
							@endif	
							@if($bannerad->Video != '')
								<div class="col-md-6  col-xs-6 clearfix d-none d-md-block">
								<div class="card mb-2">
									<div class="embed-responsive embed-responsive-16by9">
										{!! $bannerad->Video !!}
									</div>               
								</div>
								</div>
								@php						
									$col = $col+2;
								@endphp
							@else
								<div class="col-md-3  col-xs-3">
								<div class="card mb-2">
									<a href="{{ $bannerad->ban_link }}">
										<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" class="card-img-top-home" src="{{ config('shop.img_url').'/uploads/bannerads/'.$bannerad->EnBanimage }}" alt="{{ $bannerad->ban_name }}"></a>
								</div>
								</div>	
								@php						
									++$col;
								@endphp
							@endif								  
						@endforeach
						</div>
					</div>
				</div>
				<!--/.Slides-->
			</div>
		<!--/.Carousel Wrapper-->

		<!--Carousel Wrapper for mobile-->
		<div id="carouselExampleControls" class="carousel slide d-block d-md-none" data-ride="carousel">
		<div class="carousel-inner">
			@foreach($bannerads as $bk => $bannerad)
				<div class="carousel-item @if($bk == 0) active @endif">
				@if($bannerad->Video != '')
					<div class="embed-responsive embed-responsive-16by9">
						{!! $bannerad->Video !!}
					</div> 
				@else					
					<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" class="d-block w-100" src="{{ config('shop.img_url').'/uploads/bannerads/'.$bannerad->EnBanimage }}" alt="{{ $bannerad->ban_name }}">
				@endif
				</div>
			@endforeach
		</div>
		<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
		</div>
		<!--/.Carousel Wrapper for mobile-->
	</div>
	</section>
	@endif

	@if($homecategories->isNotEmpty())
	<section class="mb-5 mt-5">
		<div class="container">
			<div class="section-title mb-4">
				<h2>Categories</h2>
				<hr style="width:50px; border-bottom:4px solid #000;"></hr>
			</div>
				<div class="row">
				@foreach($homecategories as $homecategory)
					@php 
						$productscount = 0; 
						$catproducts = \App\Models\Product::where(['Types' => $homecategory->TypeId,'ProdStatus' => '1'])->count();
						$productscount = $productscount + $catproducts; 
						$fallback = config('shop.fallback_img');
						$image = trim((string) $homecategory->Image) !== ''
							? config('shop.img_url') . '/uploads/category/' . $homecategory->Image
							: $fallback;

						$subcats = $homecategory->children;
					@endphp	
					@foreach($subcats as $subcat)
						@php 
							$productscount = $productscount + $subcat->products_count;							
							$subsubcats = \App\Models\Category::where(['ParentLevel' => $subcat->TypeId, 'TypeStatus' => 1])
								->withCount(['products' => fn ($q) => $q->where('ProdStatus', '1')])
								->orderBy('EnName')->get();
						@endphp	
						@foreach($subsubcats as $subsubcat)
							@php 
								$productscount = $productscount + $subsubcat->products_count;
							@endphp
						@endforeach
					@endforeach
					
					<div class="col-lg-3 col-md-4 col-sm-6">
						<div class="">
							<a href="{{ url('type/'.$homecategory->UniqueKey) }}">
								<img src="{{ $image }}" alt="{{ $homecategory->EnName }}" class="homecat" loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';">
								<div class="caption">
									<h4 class="pt-3 pb-1">{{ $homecategory->EnName }}</h4>
									<p>{{ $productscount }} Products</p>
								</div>
							</a>
						</div>
					</div>
				@endforeach
				</div>
		</div>
		<div class="text-center mt-4 mb-4">
			<a href="{{ url('types') }}" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL</a></div>
	</section>
	@endif
<!-- Product imaes end -->

<!-- banner  start -->
@php
	$homeban1 = \App\Models\MastheadImage::where('ban_status', '=', '1')->orderBy('display_order', 'asc')->skip(0)->take(1)->first();
@endphp
@if($homeban1)
<section>	
	<div><a href="{{ $homeban1->ban_link }}">
		<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" src="{{ config('shop.img_url').('/uploads/bannermaster/'.$homeban1->EnBanimage) }}" alt="{{ $homeban1->ban_name }}" style="width:100%;"></a></div>
</section>
@endif
<!-- banner  end -->
	
	@if($homebrands->isNotEmpty())
	<section class="add-section">
		<div class="container my-4 mb-5 newbox">
			<div class="section-title mb-4">
				<h2>Brands</h2>
				<hr style="width:50px; border-bottom:4px solid #000;"></hr>
			</div>
				<div class="row">
				@foreach($homebrands as $homebrand)	
					@php
						$fallback = config('shop.fallback_img');
						$image = trim((string) $homebrand->Image) !== ''
							? config('shop.img_url') . '/uploads/brands/' . $homebrand->Image
							: $fallback;
					@endphp
					<div class="col-lg-2 col-md-4 col-sm-6 col-6">	
						<a href="{{ url('brand/'.$homebrand->UniqueKey) }}">
							<img loading="lazy" onerror="this.onerror=null;this.src='{{ $fallback }}';" src="{{ $image }}" alt="{{ $homebrand->EnName }}" style="width:100%; margin:0px 0px 21px 0px;">
						</a>
					</div>
				@endforeach
				</div>
		</div>
		<div class="text-center pb-5 ">
			<a href="{{ url('brands') }}" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL</a>
		</div>
	</section>
	@endif

	@if($homeban2)
	<section>	
		<div>
			<a href="{{ $homeban2->ban_link }}">
				<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" src="{{ config('shop.img_url').('/uploads/bannermaster/'.$homeban2->EnBanimage) }}" alt="{{ $homeban2->ban_name }}" style="width:100%;">
			</a>
		</div>
	</section>
	@endif
	
<!-- About section start-->
<section class="mt-5 mb-5">
<div class="container">
	<div class="row">
		<div class="col-md-4">
			<div>
			@if($welcomecontent && $welcomecontent->banner_image != '')
				<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" src="{{ config('shop.img_url').('/uploads/pagecontent/'.$welcomecontent->banner_image) }}" alt="" style="width:100%;">
			@else
				<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" src="{{ asset('img/ads/about.jpg') }}" alt="" style="width:100%;">
			@endif
			</div>
		</div>
		@if($welcomecontent)
		<div class="col-md-8">
			<div class="mt-2">
				<h2>About<br>HardwareCity®</h2>
				<hr style="width:50px; float:left; border-bottom:4px solid #000;"></hr>
			</div>
			<div style="clear:both;">
			{!! $welcomecontent->ChContent !!}
			</div>
		</div>
		@endif	
	</div>
</div>
</section>


@include('footer')

<script type="text/javascript">
function addtocart(ProdId) {
	var redirecturl = '{{ $currenturl }}';
	var token = "{!! csrf_token() !!}";
	$('#addcart'+ProdId).html('Adding...');
	$.ajax({
		type: "POST",
		url: '{{ url("/") }}/addtocart',				
		data: {'prodid': ProdId},
		headers: {'X-CSRF-TOKEN': token},		
		success: function(response) {
			$('#addcart'+ProdId).html('ADD TO CART');
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