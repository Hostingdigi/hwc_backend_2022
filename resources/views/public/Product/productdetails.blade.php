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
		<div class="card-deck sg-product">
				
			<div class="col-md-12">
				<div class="row">					
					<div class="col-md-4">
						<div class="sg-img">
							<!-- Tab panes -->
							<div class="tab-content">
								<div class="tab-pane fade show active" id="sg1" role="tabpanel">
									@if($productdetail->LargeImage)
										<img src="{{ url('/uploads/product/'.$productdetail->LargeImage) }}" class="img-fluid" alt="{{ $productdetail->EnName }}">
									@else
										<img src="{{ url('images/noimage.png') }}" class="img-fluid" alt="{{ $productdetail->EnName }}">
									@endif									
								</div>
								@if($galleries)
									@php $g = 2; @endphp
									@foreach($galleries as $gallery)
										<div class="tab-pane" id="sg{{ $g }}" role="tabpanel">
											@if($gallery->LargeImage)
												<img src="{{ url('/uploads/product/'.$gallery->LargeImage) }}" alt="" class="img-fluid">
											@else
												<img src="{{ url('images/noimage.png') }}" class="img-fluid" alt="{{ $productdetail->EnName }}">
											@endif
										</div>
										@php ++$g; @endphp
									@endforeach
								@endif																									
							</div>
							<div class="nav d-flex justify-content-between">
								@if($productdetail->Image)
									<a class="nav-item nav-link active" data-toggle="tab" href="#sg1"><img src="{{ url('/uploads/product/'.$productdetail->Image) }}" alt="{{ $productdetail->EnName }}"></a>
								@else
									<a class="nav-item nav-link active" data-toggle="tab" href="#sg1"><img src="{{ url('images/noimage.png') }}" alt="{{ $productdetail->EnName }}"></a>
								@endif
								@if($galleries)
									@php $g = 2; @endphp
									@foreach($galleries as $gallery)
										<a class="nav-item nav-link" data-toggle="tab" href="#sg{{ $g }}">
											@if($gallery->Image)
												<img src="{{ url('/uploads/product/'.$gallery->Image) }}" alt="" class="img-fluid">
											@else
												<img src="{{ url('images/noimage.png') }}" class="img-fluid" alt="{{ $productdetail->EnName }}">
											@endif
										</a>
										@php ++$g; @endphp
									@endforeach
								@endif								
							</div>
							
							@if(!empty($productdetail->Video))
								<div class="mt-3">
								{!! $productdetail->Video !!}
								</div>
							@endif
							
						</div>					 
					</div>
			  
			  
			  <div class="col-md-8">
			 <div class="card ">
					@php
						$maxqty = $productdetail->Quantity;
						if(($productdetail->cust_qty_per_day < $productdetail->Quantity) && $productdetail->cust_qty_per_day > 0) {
							$maxqty = $productdetail->cust_qty_per_day;
						}
					@endphp
					
				  <h3 class="mt-2 mb-3">{{ $productdetail->EnName }}</h3>
				  
				  <div>
					<div class="product__details__text">
                      
                      <!--div class="product__details__text">
						@php
							$productrating = 0;
						@endphp
                        <div class="rating">
							@if($rating >= 5) 
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							@else
								@php									
									$productrating = 5 - (int)$rating;
									for($r = 0; $r < $rating; $r++) {
										echo '<i class="fa fa-star"></i>';
									}
									if($productrating > 0) {
										for($r = 0; $r < $productrating; $r++) {
											echo '<i class="fa fa-star-o"></i>';
										}
									}
								@endphp								
							@endif
                            @if(count($reviews) > 0)<span>( {{ count($reviews) }} reviews )</span>@endif
                        </div>
                        
                       
                    </div-->  
					
					@php	
					    $roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
						$displayprice = $productdetail->Price;
						$price = new \App\Models\Price();
						$actualprice = $roundObj->roundDecimal($price->getGroupPrice($productdetail->Id));
						$displayprice = $roundObj->roundDecimal($price->getDiscountPrice($productdetail->Id));
						$gstprice = $price->getGSTPrice($displayprice, 'SG');
						$actualgstprice = $price->getGSTPrice($actualprice, 'SG');
						//$displayprice = $price->getPrice($productdetail->Id);
					@endphp
					<input type="hidden" name="displayprice" id="displayprice" value="{{ $displayprice }}">
					<input type="hidden" name="productprice" id="productprice" value="{{ $displayprice }}">

					@if($productdetail->Price > 0)
						<div class="row">
						<div class="col-5 prod-price-col">Price</div>
						@if($displayprice < $actualprice)
						<div class="col-5 prod-price-col-red">Promotion</div>
							@endif
						</div>
						<div class="row mb-3">
							@if($displayprice < $actualprice)
						<div class="col-5">
							<div class="oldrate flft" id="oldprice">
								S${{ number_format($actualprice, 2) }}
							</div>
							<!-- <div class="color-grey-tax">[ before GST / local tax ]</div>-->
							<!-- <div style="clear:both"></div>-->
							<!-- <div class="oldrate flft" id="oldgstprice">-->
								<!--S${{ number_format($actualgstprice, 2) }}-->
							<!-- </div>-->
							<div class="color-grey-tax">[ with GST ]</div>
						</div>
						
						<div class="col-5">
							<div class="newrate flft redclr" id="actualprice">
								S${{ number_format($displayprice, 2) }}
							</div>
							<!-- <div class="color-red-tax">[ before GST / local tax ]</div>-->
							<!-- <div style="clear:both"></div>-->
							<!-- <div class="newrate flft redclr" id="actualgstprice">-->
								<!--S${{ number_format($gstprice, 2) }}-->
							<!-- </div>-->
							<div class="color-red-tax">[ with GST ]</div>
						</div>
						@else
							<div class="col-5">
							<div class="newrate flft blackclr" id="actualprice">
								S${{ number_format($displayprice, 2) }}
							</div>
							<!--<div class="color-black-tax">[ before GST / local tax ]</div>-->
							<!--<div style="clear:both"></div>-->
							<!--<div class="newrate flft blackclr" id="actualgstprice">-->
								<!--S${{ number_format($gstprice, 2) }}-->
							<!--</div>-->
							<div class="color-black-tax">[ with GST ]</div>
						</div>
						@endif
						</div>
					@else
						<div class="row">
							<div class="col-5 prod-price-col-red">COMING SOON</div>	
						</div>
					@endif
					<!--div class="row mb-3">
					@if($displayprice < $actualprice)
					<div class="col-4">					
						<div class="oldrate flft">
							${{ number_format($actualprice, 2) }}
						</div>
					</div>
					@endif
					
					<div class="col-4">
						<div class="newrate flft" id="actualprice">						
						${{ number_format($displayprice, 2) }}						
						</div>
					</div>
					
					
				  </div-->
						@if(count($options) > 0 && $productdetail->Price > 0)
						<div class="row mb-3">
							<div class="col-md-6">
								<select name="options" id="options" class="form-control" required>
									<option value="">Select Option</option>
									@foreach($options as $option)										
										<option value="{{ $option->Id }}">{{ $option->Title }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<input type="hidden" name="optionsproduct" id="optionsproduct" value="YES">
						@else
							<input type="hidden" name="optionsproduct" id="optionsproduct" value="NO">
						@endif
						
						{!! $productdetail->ProdCode !!}
                        <!--p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since</p-->
						
						@if($productdetail->Quantity > 0 && $productdetail->Price > 0)
						
                        <div class="product__details__button">
                            <div class="quantity">
                                <span>Quantity : </span>
                                <div class="pro-qty">
                                    <input type="text" value="1" id="qty" name="qty">
                                </div>
                            </div>
                           
                        </div>
						
						@endif
                       
                    </div>
				  </div>
				  <div class=" mt-1">
				  @if($productdetail->Price > 0)
						@if($productdetail->Quantity > 0)
							@if($productdetail->ProdStatus == 1)
							<a href="javascript:void(0);" onclick="addtocart('{{ $productdetail->Id }}');" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a>
							@else
							<a href="javascript:void(0);" class="site-btn bg-dark mx-auto textyellow">Item is Not Available</a>
							@endif
						@else
							<a href="javascript:void(0);" class="site-btn bg-dark mx-auto textyellow">Out of Stock</a>
							<div class="emailus"><a href="javascript:void(0);" class="site-btn bg-dark mx-auto textyellow" data-toggle="modal" data-target="#enquiryModal">Email US for More Enquiry</a></div>
						@endif
					@endif
				  </div>
				  
				  <div class="row">
                <div class="col-lg-12">
                    <div class="product__details__tab sg-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Specification</a>
                            </li>
							<li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Download</a>
                            </li>
							<li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-4" role="tab">Enquiry</a>
                            </li>
                            <!--li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#rev" role="tab">Reviews @if(count($reviews) > 0) ({{ count($reviews) }}) @endif</a>
                            </li-->
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-1" role="tabpanel">								
								<div class="row" style="margin-top:15px;">
									
										<div class="col-md-12 col-sm-12">
											@if($productdetail->EnInfo) {!! $productdetail->EnInfo !!} @else N/A @endif
											@if($productdetail->Color != '')
												<br>Color: {{ $productdetail->Color }}
											@endif
											@if($productdetail->Weight != '')
												<br>Weight(KG): {{ $productdetail->Weight }}
											@endif
											@if($productdetail->Dimension != '')
												<br>Dimension: {{ $productdetail->Dimension }}
											@endif
										</div>
									
								</div>		
                            </div>
                            <div class="tab-pane" id="tabs-2" role="tabpanel">
								@if($productdetail->Specs) {!! $productdetail->Specs !!} @else N/A @endif
                            </div>
							<div class="tab-pane" id="tabs-3" role="tabpanel">
								@if($productdetail->Tds != '' && file_exists(public_path('/uploads/product/'.$productdetail->Tds)))
									<img src="{{ url('/img/icon-pdf.jpg') }}"> TDS : <a href="{{ url('/uploads/product/'.$productdetail->Tds) }}" download>{{ $productdetail->EnName }}</a><br><br>
								@endif	
								@if($productdetail->Sds != '' && file_exists(public_path('/uploads/product/'.$productdetail->Sds)))
									<img src="{{ url('/img/icon-pdf.jpg') }}"> SDS : <a href="{{ urldecode(url('/uploads/product/'.$productdetail->Sds)) }}" download>{{ $productdetail->EnName }}</a>
								@endif	
                            </div>
							<div class="tab-pane" id="tabs-4" role="tabpanel">
								<div class="alert" id="qamsg" style="display:none; color:green; font-weight:bold;"></div>
								<div id="qaerror" style="display:none; color:red; text-align:center;"></div>
								<form action="" name="qa_frm">
									<input type="hidden" name="productname" id="productname" class="rating-value" value="{{ $productdetail->EnName }}">
									<input type="hidden" name="productcode" id="productcode" class="rating-value" value="{{ $productdetail->ProdCode }}">	
										<div class="row">
											<div class="col-md-6">
												<div >
													<label>Name*</label>
													<input type="text" class="form-control" required name="custname" id="custname" value="" style="margin-bottom:10px;">
												</div>
											</div>
										</div>
										<div class="row">	
											<div class="col-md-6">
												<div>
													<label>Email*</label>
													<input type="email" class="form-control" required name="custemail" id="custemail" value="" style="margin-bottom:10px;">
												</div>
											</div>
										</div>
										<div class="row">	
											<div class="col-md-6">
												<label>Question*</label>
												<textarea required name="question" id="question" style="margin-bottom:10px;"></textarea>	
												<button type="button" name="submit" class="redbtn" id="submitquestion">Submit</button>
											</div>
										</div>
									</form>
                            </div>
                            <div class="tab-pane" id="rev" role="tabpanel">
								@if($reviews)
									@foreach($reviews as $review)
									@php
										$customername = '';
										$customer = \App\Models\Customer::where('cust_id', '=', $review->CustomerId)->select('cust_firstname', 'cust_lastname')->first();
										if($customer) {
											$customername = $customer->cust_firstname.' '.$customer->cust_lastname;
										}
									@endphp
									<div class="review-box d-flex">
										<div class="rv-img">
											<img src="{{ url('images/general.png') }}" alt="">
										</div>
										<div class="rv-content">
											<h6>{{ $customername }} <span>({{ date('M d, Y', strtotime($review->created_at)) }})</span></h6>
											<ul class="list-unstyled list-inline">
												@php 
													$custrating = 0;
												@endphp
												@if((int)$review->rating == 5)
													<li class="list-inline-item"><i class="fa fa-star"></i></li>
													<li class="list-inline-item"><i class="fa fa-star"></i></li>
													<li class="list-inline-item"><i class="fa fa-star"></i></li>
													<li class="list-inline-item"><i class="fa fa-star"></i></li>
													<li class="list-inline-item"><i class="fa fa-star"></i></li>
												@else
													@php 
														$custrating = 5 - (int)$review->rating;
													@endphp
													@php
														for($x = 0; $x < $review->rating; $x++) {
															echo '<li class="list-inline-item"><i class="fa fa-star"></i></li>';
														}
														if($custrating > 0) {
															for($x = 0; $x < $custrating; $x++) {
																echo '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';
															}
														}
													@endphp
												@endif	
											</ul>
											<p>{!! $review->comments !!}</p>
										</div>
									</div>
									@endforeach
								@endif
								<div class="review-form">
									<h6>Add Your Review</h6>
									<div class="alert" id="reviewmsg" style="display:none; font-weight:bold;"></div>
									<form action="" name="rating_frm">
										<div class="row">
											<div class="col-md-12">
												<div class="star-rating">
													<label>Your Rating*</label>
													<span class="fa fa-star-o" data-rating="1" onclick="setrating(1);"></span>
													<span class="fa fa-star-o" data-rating="2" onclick="setrating(2);"></span>
													<span class="fa fa-star-o" data-rating="3" onclick="setrating(3);"></span>
													<span class="fa fa-star-o" data-rating="4" onclick="setrating(4);"></span>
													<span class="fa fa-star-o" data-rating="5" onclick="setrating(5);"></span>
													<input type="hidden" name="rating" id="rating" class="rating-value" value="0">
													<input type="hidden" name="producid" id="producid" class="rating-value" value="{{ $productdetail->Id }}">
												</div>
											</div>
											
											<div class="col-md-12">
												<label>Your Review*</label>
												<textarea required name="comments" id="comments"></textarea>
												
												
												<input type="hidden" name="custid" id="custid" @if(Session::has('customer_id')) value="{{ Session::get('customer_id') }}" @else value="0" @endif>
													
												<button type="button" name="submit" id="submitreview">Submit</button>
											</div>
										</div>
									</form>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
				  
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
    <section class="product-details sg-product">
        <div class="container">
            
                
			@if(count($relatedproducts) > 0)
				
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="related__title mt-3 mb-3">
                        <h5>RELATED PRODUCTS</h5>
                    </div>
                </div>
				
				@foreach($relatedproducts as $relatedproduct)
				
					<div class="col-md-2 col-sm-4">
						<div class="product__item">
							@if($relatedproduct->Quantity <= 0)
								<div class="box-no-product"><span class="boxAdjest">Out of Stock</span></div>
							@endif
							<div class="box-Added-product" id="success{{ $relatedproduct->Id }}" style="display:none;"><span class="boxAdjest">Item has been Added into Your Cart</span></div>
							<a href="{{ url('/prod/'.$relatedproduct->UniqueKey) }}">
							@if($relatedproduct->Image)
								<img src="{{ url('/uploads/product/'.$relatedproduct->Image) }}" class="card-img-top" alt="{{ $relatedproduct->EnName }}">
							@else
								<img src="{{ url('images/noimage.png') }}" class="card-img-top" alt="{{ $relatedproduct->EnName }}">
							@endif
							</a>
							<div class="product__item__text">
								<h6 class="mt-3 mb-2 subcatlog"><a href="{{ url('/prod/'.$relatedproduct->UniqueKey) }}" alt="{{ $relatedproduct->EnName }}" title="{{ $relatedproduct->EnName }}">@if(strlen($relatedproduct->EnName) > 27){{ substr($relatedproduct->EnName, 0, 27) }}...@else {{ $relatedproduct->EnName }} @endif</a></h6>

								@if($relatedproduct->Price >0)
									<div class="product__price">
									@php	
										$rdisplayprice = $relatedproduct->Price;
										$price = new \App\Models\Price();
										$rdisplayprice = $price->getPrice($relatedproduct->Id);
										$rdisplayprice = $price->getGSTPrice($rdisplayprice, 'SG');
									@endphp
									S${{ number_format($roundObj->roundDecimal($rdisplayprice), 2) }}
									</div>
								@else
									<div class="prod-price-col-red" style="font-size:14px;">	
										COMING SOON
									</div>					
								@endif
							</div>
							<div class="text-center mt-3">
							@if($relatedproduct->Quantity > 0)
								@php																	
									$options = \App\Models\ProductOptions::where('Prod', '=', $relatedproduct->Id)->count();
								@endphp
								 
								@if($options > 0 || $relatedproduct->Price ==0)
									<a href="{{ url('/prod/'.$relatedproduct->UniqueKey) }}" class="site-btn bg-dark mx-auto textyellow">More Details</a>
								@else
									<a href="javascript:void(0);" onclick="relatedaddtocart('{{ $relatedproduct->Id }}');" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a>
								@endif
							@else
								<a href="javascript:void(0);" class="site-btn bg-dark mx-auto textyellow">Out of Stock</a>
							@endif
							</div>
						</div>
					</div> 
				
				@endforeach
				
            </div>
			
			@endif
        </div>
    </section>
    <!-- Product Details Section End -->
<div class="modal fade" id="enquiryModal" tabindex="-1" role="dialog" aria-labelledby="enquiryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Enquiry Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<div id="emailusmsg" style="display:none"></div>
		<div id="error" style="display:none; color:red; text-align:center;"></div>
        <form name="enquiry_frm" method="post">
			<div class="row">
				<div class="col-md-12">
					<label>Name*</label>
					<input type="text" name="name" id="name" required placeholder="" maxlength="100" class="mb-15 form-control">
				</div>
				<div class="col-md-12">
					<label>Email*</label>
					<input type="email" name="email" id="email" required placeholder="" maxlength="100" class="mb-15">
				</div>				
				<div class="col-md-12">
					<label>Phone*</label>
					<input type="number" name="phone" id="phone" required placeholder="" maxlength="50" class="mb-15">
				</div>				
				<div class="col-md-12">
					<label>Message*</label>
					<textarea id="message" name="message" required class="mb-15"></textarea>
				</div>
			</div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="enquiryemailus">Submit</button>
      </div>
    </div>
  </div>
</div>

@include('footer')

<script type="text/javascript">
$(document).ready(function() {
	
	$('#options').change(function() {
		$('#options').css('border', '1px solid #ced4da');
		var optionid = $('#options').val();		
		var token = "{!! csrf_token() !!}";
		var ProdId = '{{ $productdetail->Id }}';
		var displayprice = '{{ $displayprice }}';
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/setOptionPrice',				
			data: {'prodid': ProdId, 'optionid': optionid, 'displayprice': displayprice},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {
				console.log(response);
				var result = response.split("#");
				$('#actualprice').html('$'+result[0]);
				$('#productprice').val(result[0]);
				$('#actualgstprice').html('$'+result[1]);
				if(result[2] == 1) {
					$('#oldprice').html('$'+result[3]);
					$('#oldgstprice').html('$'+result[4]);
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});		
	});
	
	$('#submitreview').click(function() {
		var custid = $('#custid').val();
		
		if(custid > 0) {
			var rating = $('#rating').val();
			var comments = $('#comments').val();
			var token = "{!! csrf_token() !!}";
			var producid = $('#producid').val();
			
			$.ajax({
				type: "POST",
				url: '{{ url("/") }}/productrating',				
				data: {'prodid': producid, 'rating': rating, 'comments': comments, 'custid':custid},
				headers: {'X-CSRF-TOKEN': token},		
				success: function(response) {
					$('#reviewmsg').html('Your Review Successfully Updated');
					$('#reviewmsg').show();
				},error: function(ts) {				
					console.log("Error:"+ts.responseText);  
				}
			});
		} else {
			$('#reviewmsg').html('Please login to review this product!');
			$('#reviewmsg').show();
		}
	});
	
	$('#submitquestion').click(function() {
		var email = $('#custemail').val();
		var name = $('#custname').val();
		var question = $('#question').val();
		var token = "{!! csrf_token() !!}";
		var productname = $('#productname').val();
		var productcode = $('#productcode').val();
		
		if(name != '' && email != '' && question != '') {
		    if(validateEmail(email)) {
    			$.ajax({
    				type: "POST",
    				url: '{{ url("/") }}/submitqa',				
    				data: {'productname': productname, 'productcode': productcode, 'name': name, 'email': email, 'question':question},
    				headers: {'X-CSRF-TOKEN': token},		
    				success: function(response) {
    					$('#qaerror').hide();
    					$('#qamsg').html('Your Enquiry Successfully Submitted');
    					$('#qamsg').show();
    					$('#custemail').val('');
    					$('#custname').val('');
    					$('#question').val('');
    				},error: function(ts) {				
    					console.log("Error:"+ts.responseText);  
    				}
    			});
		    } else {
    			$('#qaerror').html('Invalid Email Format');
    			$('#qaerror').show();
    		}
		} else {
			$('#qaerror').html('All fields are required*');
			$('#qaerror').show();
		}
	});
	
	$('#enquiryemailus').click(function() {
		var name = $('#name').val();		
		var email = $('#email').val();
		var phone = $('#phone').val();
		var message = $('#message').val();
		var productname = $('#productname').val();
		var token = "{!! csrf_token() !!}";
		if(name != '' && email != '' && phone != '' && message != '') {
			if(validateEmail(email)) {
				$('#error').hide();
				$.ajax({
					type: "POST",
					url: '{{ url("/") }}/enquiryemailus',				
					data: {'name': name, 'email': email, 'phone': phone, 'message':message, 'productname': productname},
					headers: {'X-CSRF-TOKEN': token},		
					success: function(response) {
						$('#emailusmsg').html('Enquiry Details Successfully Submitted');
						$('#emailusmsg').show();
						$('#name').val('');
						$('#email').val('');
						$('#phone').val('');
						$('#message').val('');
					},error: function(ts) {				
						console.log("Error:"+ts.responseText);  
					}
				});
			} else {
				$('#error').html('Invalid Email Format');
				$('#error').show();
			}
		} else {			
			$('#error').html('All fields are mandatory.');
			$('#error').show();
		}
	});
	
	$('#name').keydown(function (e) {
        if (e.ctrlKey || e.altKey) {
             e.preventDefault();
        } else {
            var key = e.keyCode;
			
            if(!((key==8)||(key==9)||(key==32)||(key==46)||(key>=65 && key<=90)||(key==190))) {
                e.preventDefault();
            }
        }
    });
});

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function addtocart(ProdId) {
	var redirecturl = '{{ $currenturl }}';
	var qty = $('#qty').val();
	var optionid = 0;	
	var token = "{!! csrf_token() !!}";
	var optionsproduct = $('#optionsproduct').val();
	var allowSubmit = 'YES';
	var maxqty = "{{ $maxqty }}";
	if(optionsproduct == 'YES') {
		var options = $('#options').val();
		if(options == '') {
			allowSubmit = 'NO';
			$('#options').css('border', '1px solid red');
		} else {
			$('#options').css('border', '1px solid #ced4da');
		}
	}
	
	
	if(allowSubmit == 'YES') {
		if(parseInt(qty) > parseInt(maxqty)) {
			window.location = "{{ url('/addtocartstatus/?returnurl=') }}"+redirecturl+"&pid="+ProdId+"&status=notavailable";
		} else {
			$.ajax({
				type: "POST",
				url: '{{ url("/") }}/addtocart',				
				data: {'prodid': ProdId, 'qty': qty, 'optionid': options},
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
	}
}

function relatedaddtocart(ProdId) {
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

function setrating(rating) {
	$('#rating').val(rating);
}
</script>