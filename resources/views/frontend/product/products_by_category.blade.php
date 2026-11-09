@extends('layouts.app')
@section('content')
@includeIf('includes.breadcrumb',['grand_parent_name' => $grandparentname, 'grand_parent_url' => $grandparenturl, 
	'parent_name' => $parentname, 'parent_url' => $parenturl, 'page_title' => $categoryname])
@php
    $roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
@endphp
<!-- Category Area -->
<section class="category">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				@includeIf('includes.brands',['brands' => $brands, 'bid' => $bid])
				@includeIf('includes.tags',['tags' => $producttags])
				@includeIf('includes.banner', ['banners' => $adbanners])
			</div>
			<div class="col-md-9">
				<div class="product-box">
					<div class="cat-box d-flex justify-content-between">
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
								<option value="20" @if($show == 20) selected @endif>20</option>
								<option value="28" @if($show == 28) selected @endif>28</option>
								<option value="36" @if($show == 36) selected @endif>36</option>
							</select>
						</div>
						
						<div class="page" style="display:none;">
							@if($products->isEmpty())
							<p>Page 0 of 0</p>
							@else
							<p>Page {{ $page }} of {{ $products->lastPage() }}</p>
							@endif
						</div>
					</div>
					<!-- Tab panes -->
					@if($products->isEmpty())
						<div align="center">No Products Found!</div>
					@endif	
					<div class="tab-content">
						<div class="tab-pane fade show active" id="grid" role="tabpanel">
							<div class="row" id="productslist">	
								@includeWhen($products->isNotEmpty(), 'includes.products', $products)
							</div>
						</div>
					</div>
					
					<div class="pagination-box" style="float:right;">
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
 @endsection
 @push('child-scripts')
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
	var redirecturl = '{{ \Request::url() }}';
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
@endpush