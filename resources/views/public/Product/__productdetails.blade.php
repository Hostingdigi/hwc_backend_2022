@include('header')

    
<section id="breadcrumbs" class="breadcrumbs">
  <div class="container">
	<ol>
		<li><a href="{{ url('/') }}">Home</a></li>
		@if($category)
			@if($subcategory == '')
				<li><a href="{{ url('/category/'.$categoryurl) }}">{{ $category }}</a></li>
			@else
				<li><a href="{{ url('/type/'.$categoryurl) }}">{{ $category }}</a></li>
			@endif
		@endif
		@if($subcategory)
			@if($childcategory == '')
				<li><a href="{{ url('/category/'.$subcategoryurl) }}">{{ $subcategory }}</a></li>
			@else
				<li><a href="{{ url('/type/'.$subcategoryurl) }}">{{ $subcategory }}</a></li>
			@endif
		@endif
		@if($childcategory > 0)
			<li><a href="{{ url('/category/'.$childcategoryurl) }}">{{ $childcategory }}</a></li>
		@endif
		<li><div class="pageheading">{{ $productdetail->EnName }}</div></li>
	</ol>
  </div>
</section>
@php 
	$currenturl = \Request::url(); 	
@endphp

@includeIf('public.messages')
<section class="top-letest-product-section mt-4">
		<div class="container">
			<!--div class="col-12 section-title">
				<h2>Product List</h2>
			</div-->
			<div class="card-deck">
		
					
			  <div class="col-md-12">
					<div class="row">
											
							 
			  <div class="col-md-6">
			  <div class="card ">
				@if($productdetail->LargeImage)
					<img src="{{ url('/uploads/product/'.$productdetail->LargeImage) }}" class="card-img-top" alt="{{ $productdetail->EnName }}">
				@else
					<img src="{{ url('images/noimage.png') }}" class="card-img-top" alt="{{ $productdetail->EnName }}">
				@endif
				<div class="card-body">
				<div class="heartIcon"><a href="#"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px; color: #999; font-size: 21px;"></i></a></div>
				 
				  
				  <div>
					
				  </div>
				  
				  
				</div>
			  </div>
			  </div>
			  
			  <div class="col-md-6">
			 <div class="card ">
				

				  <h3 class="mt-2 mb-3">{{ $productdetail->EnName }}</h3>
				  
				  <div>
					<div class="product__details__text">
                      
                      <div class="product__details__text">
                      
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <span>( 138 reviews )</span>
                        </div>
                        
                       
                    </div>  
					@php	
						$displayprice = $productdetail->StandardPrice;
						$price = new \App\Models\Price();
						$displayprice = $price->getPrice($productdetail->Id);
					@endphp
					<input type="hidden" name="displayprice" id="displayprice" value="{{ $displayprice }}">
					<input type="hidden" name="productprice" id="productprice" value="{{ $displayprice }}">
					<div class="row mb-3">
					@if($displayprice < $productdetail->StandardPrice)
					<div class="col-4">					
						<div class="oldrate flft">
							${{ number_format($productdetail->StandardPrice, 2) }}
						</div>
					</div>
					@endif
					
					<div class="col-4">
						<div class="newrate flft" id="actualprice">						
						${{ number_format($displayprice, 2) }}						
						</div>
					</div>
				  </div>
						@if($options)
						<div class="row mb-3">
							<div class="col-md-6">
								<select name="options" id="options" class="form-control">
									<option value="">Select Option</option>
									@foreach($options as $option)
										<option value="{{ $option->Id }}">{{ $option->Title }}</option>
									@endforeach
								</select>
							</div>
						</div>
						@endif
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since</p>
                        <div class="product__details__button">
                            <div class="quantity">
                                <span>Quantity : </span>
                                <div class="pro-qty">
                                    <input type="text" value="1">
                                </div>
                            </div>
                           
                        </div>
                       
                    </div>
				  </div>
				  <div class=" mt-1"><a href="javascript:void(0);" onclick="addtocart('{{ $productdetail->Id }}');" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				  
				</div>
			  </div>
			  </div>
						
					</div>
				</div>
			 
			</div>

		</div>
	</section>
	<!-- letest product section end -->



    <!-- Product Details Section Begin -->
    <section class="product-details">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product__details__tab" style="margin-top:-40px;">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Specification</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Reviews</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-1" role="tabpanel">
								<div class="row" style="margin-top:15px;">
									@if(!empty($productdetail->Video)) 
										<div class="col-md-4 col-sm-12">
											<iframe width="100%" height="300" src="{{ $productdetail->Video }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
										</div>
										<div class="col-md-8 col-sm-12">
											{!! $productdetail->EnInfo !!}
										</div>
									@else
										<div class="col-md-12 col-sm-12">
											{!! $productdetail->EnInfo !!}
										</div>
									@endif
								</div>
                            </div>
                            <div class="tab-pane" id="tabs-2" role="tabpanel">
								{!! $productdetail->Spec !!}
                            </div>
                            <div class="tab-pane" id="tabs-3" role="tabpanel">
                                <h6 class="mt-3 mb-3">Reviews</h6>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever sinceLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever sinceLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever sinceLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever sinceLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since</p>
                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget
                                    dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes,
                                    nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium
                                quis, sem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			@if(count($relatedproducts) > 0)
				
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="related__title mt-3 mb-3">
                        <h5>RELATED PRODUCTS</h5>
                    </div>
                </div>
				
				@foreach($relatedproducts as $relatedproduct)
				
					<div class="col-md-3">
						<div class="product__item">
							@if($relatedproduct->Image)
								<img src="{{ url('/uploads/product/'.$relatedproduct->Image) }}" class="card-img-top" alt="{{ $relatedproduct->EnName }}">
							@else
								<img src="{{ url('images/noimage.png') }}" class="card-img-top" alt="{{ $relatedproduct->EnName }}">
							@endif
							<div class="product__item__text">
								<h6 class="mt-3 mb-2 subcatlog"><a href="{{ url('/prod/'.$relatedproduct->UniqueKey) }}">{{ $relatedproduct->EnName }}</a></h6>
								<div class="rating">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
								</div>
								<div class="product__price">$ 179.00</div>
							</div>
						</div>
					</div> 
				
				@endforeach
				
            </div>
			
			@endif
        </div>
    </section>
    <!-- Product Details Section End -->


@include('footer')

<script type="text/javascript">
function addtocart(ProdId) {
	var redirecturl = '{{ $currenturl }}';
	var qty = $('#qty').val();
	var token = "{!! csrf_token() !!}";
	$.ajax({
		type: "POST",
		url: '{{ url("/") }}/addtocart',				
		data: {'prodid': ProdId, 'qty': qty},
		headers: {'X-CSRF-TOKEN': token},		
		success: function(response) {
			console.log(response);
			if(response == 'Success') {
				window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=success";
			} else {
				window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=failed";
			}
		},error: function(ts) {				
			console.log("Error:"+ts.responseText);  
		}
	});
}
</script>