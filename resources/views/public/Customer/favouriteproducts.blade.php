@include('header')

<!-- Header section end -->
<section id="breadcrumbs" class="breadcrumbs">
  <div class="container">

	<ol>
	  <li><a href="{{ url('/') }}">Home</a></li>
	  <li><div class="pageheading">Wishlist</div></li>
	</ol>
  </div>
</section>
@php 
	$currenturl = \Request::url(); 	
@endphp
@includeIf('public.messages')
<!-- Wishlist -->
<section class="shopping-cart">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<div class="cart-table wsh-list table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th class="t-pro">Product</th>
								<th class="t-price">Price</th>
								<th class="t-qty">Stock</th>
								<th class="t-total">Add To Cart</th>
								<th class="t-rem"></th>
							</tr>
						</thead>
						<tbody>
						@if($products)
							@foreach($products as $product)
							<tr>
								<td class="t-pro d-flex">
									<div class="t-img">
										<a href="{{ url('/prod/'.$product->UniqueKey) }}">
										@if($product->Image != '')
											<img src="{{ url('/uploads/product/'.$product->Image) }}" width="100" alt="{{ $product->EnName }}">
										@else
											<img src="{{ url('/images/noimage.png') }}" width="100" alt="{{ $product->EnName }}">
										@endif
										</a>
									</div>
									<div class="t-content">
										<p class="t-heading"><a href="">{{ $product->EnName }}</a></p>
										<!--div class="wid_full">
										@php
											$productrating = '';
											$rating = new \App\Models\ProductReviews();
											$productrating = $rating->productRating($product->Id);
										@endphp
										{!! $productrating !!}
										</div-->
									</div>
								</td>
								<td class="t-price">
									@php	
										$displayprice = $product->StandardPrice;
										$price = new \App\Models\Price();
										$displayprice = $price->getPrice($product->Id);
									@endphp
									${{ number_format($displayprice, 2) }}
								</td>
								<td class="t-stk">@if($product->Quantity > 0) In Stock @else Out of Stock @endif</td>
								<td class="t-add">
								@if($product->Quantity > 0)
									@php																	
										$options = \App\Models\ProductOptions::where('Prod', '=', $product->Id)->count();
									@endphp
									 
									@if($options > 0)
										<button type="button" name="button" class="site-btn bg-dark mx-auto textyellow" onclick="viewdetails('{{ $product->UniqueKey }}');">View Details</button>
									@else
										<button type="button" name="button" class="site-btn bg-dark mx-auto textyellow" onclick="addtocart('{{ $product->Id }}');">Add to Cart</button>
									@endif
								@else
									<button type="button" name="button" class="site-btn bg-dark mx-auto textyellow">Out of Stock</button>
								@endif
								</td>
								<td class="t-rem"><a href="{{ url('/removefavproduct/'.$product->Id) }}"><i class="fa fa-trash-o"></i></a></td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="5" style="text-align:center;">No Items Found!</td>
							</tr>
						@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Wishlist -->

@include('footer')

<script type="text/javascript">
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
				window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=success";
			} else {
				window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=failed";
			}
		},error: function(ts) {				
			console.log("Error:"+ts.responseText);  
		}
	});
}

function viewdetails(prodkey) {
	window.location = "{{ url('/prod/') }}"+prodkey;
}
</script>