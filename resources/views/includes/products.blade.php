@foreach($products as $product)
@php
	$name = $product->EnName;
	$id = $product->Id;
@endphp
<div class="col-lg-3 col-md-6 col-6">
	<div class="tab-item">
		<div class="tab-img">
			@if($product->Quantity <= 0)
				<div class="box-no-product"><span class="boxAdjest">Out of Stock</span></div>
			@endif
			<div class="box-Added-product" id="success{{ $id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Cart</span></div>
			<div class="box-Added-product" id="setfav{{ $id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Favourite List</span></div>
			<div class="box-no-product" id="setfaverror{{ $id }}" style="display:none;"><span class="boxAdjest">Please Login to Add Favourite</span></div>
			<div class="img-box">
			<a href="{{ url('prod/'.$product->UniqueKey) }}">
			@if($product->Image != '')
				<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" class="main-img img-fluid" src="{{ url('uploads/product/'.$product->Image) }}"  alt="{{ $name }}" title="{{ $name }}">
				<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" class="sec-img img-fluid" src="{{ url('uploads/product/'.$product->Image) }}" alt="{{ $name }}" title="{{ $name }}">
			@else
				<img loading="lazy" onerror="this.onerror=null;this.src='{{ config('shop.fallback_img') }}';" class="main-img img-fluid" src="{{config('shop.fallback_img')}}" alt="{{ $name }}" title="{{ $name }}">
			@endif
			</a>
			</div>
			@if(in_array($id, $favproducts))
				<div class="heartIcon"><a href="#"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px; color: #999; font-size: 21px;"></i></a></div>
			@else
				<div class="layer-box" id="heart{{ $id }}">     
					<a href="javascript:void(0);" class="it-fav" data-toggle="tooltip" data-placement="left" title="Favourite" onclick="setfavorite('{{ $id }}')">
						<img loading="lazy" src="{{ url('/images/it-fav.png') }}" alt="">
					</a>
				</div>
			@endif
		</div>
		<div class="tab-heading">
			<p><a href="{{ url('prod/'.$product->UniqueKey) }}" alt="{{ $name }}" title="{{ $name }}">@if(strlen($name) > 30){{ substr($name, 0, 30) }}...@else {{ $name }} @endif</a></p>
		</div>
		<div class="img-content d-flex justify-content-between">
			<div class="wid_full">
				@if($product->Price >0)
					<ul class="list-unstyled list-inline price">
						@php	
							$displayprice = $product->Price;
							$price = new \App\Models\Price();
							$actualprice = $price->getGroupPrice($id);
							$displayprice = $price->getDiscountPrice($id);
							$installmentPrice = $price->getInstallmentPrice($displayprice);
							
							$actualprice = $roundObj->roundDecimal($price->getGSTPrice($actualprice, 'SG'));
							$displayprice = $roundObj->roundDecimal($price->getGSTPrice($displayprice, 'SG'));
							$installmentPrice = $roundObj->roundDecimal($price->getInstallmentPrice($displayprice));
						@endphp
						<li class="list-inline-item {{($displayprice < $actualprice) ? '' : 'text-center'}}">
							S${{ number_format($displayprice, 2) }}
						</li>
						@if($displayprice < $actualprice)
						<li class="list-inline-item strikeoutprice">S${{ number_format($actualprice, 2) }}</li>
						@endif
					</ul>
				@else
					<div class="text-center mt-3">
						<div class="prod-price-col-red">COMING SOON</div>	
					</div>
					<div class="text-center mt-3" style="margin-top:2.8rem!important;">Stay Tuned</div>
				@endif
				<div class="text-center mt-3">
				@if($product->Quantity > 0)
					@if($product->options()->exists() || $product->Price ==0)
						<a href="{{ url('/prod/'.$product->UniqueKey) }}" class="site-btn bg-dark mx-auto textyellow">More Details</a>
					@else
						<a href="javascript:void(0);" onclick="addtocart('{{ $id }}');" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a>
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