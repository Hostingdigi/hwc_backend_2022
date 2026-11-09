@include('header')
@includeIf('includes.breadcrumb', ['page_title' => 'Promotional Products'])
@php 
	$currenturl = \Request::url();
	$roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
@endphp
<!-- Category Area -->
<section class="category">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				@includeIf('includes.brands',['brands' => $brands, 'bid' => $bid])
				@includeIf('includes.categories', ['categories' => $categories, 'catid' => $catid])
				@if(request()->filled('cid') || request()->filled('bid'))
				<div class="view-all sidelist"><a href="{{ url('promotions') }}">Clear Filter</a></div>
				@endif
				@includeIf('includes.tags',['tags' => $producttags])
				@includeIf('includes.banner', ['banners' => $adbanners])
			</div>
			
			<div class="col-md-9">
				<div class="product-box">
					<div class="cat-box d-flex justify-content-between">
						<div class="sortby">
							<span>Sort By</span>
							<select class="sort-box" id="sortby" @if($products->isEmpty()) disabled="disabled" @endif name="sortby">
								@foreach (config('app.data_sort') as $ds)
								<option value="{{$ds['key']}}" @if($sortby == $ds['key']) selected @endif>{{$ds['name']}}</option>
								@endforeach
							</select>
						</div>
						<div class="show-item">
							<span>Show</span>
							<select class="show-box" id="showrecord" @if($products->isEmpty()) disabled="disabled" @endif name="showrecord">
								@foreach (config('app.data_length') as $length)
								<option value="{{$length}}" @if($show == $length) selected @endif>{{$length}}</option>
								@endforeach
							</select>
						</div>
						
						<div class="page" style="display:none;">
							@if($products->total() == 0)
								<p>Page 0 of 0</p>
							@else
								<p>Page {{ $page }} of {{ $products->lastPage() }}</p>
							@endif
						</div>
					</div>
					<!-- Tab panes -->
					@if($products->isEmpty())
						<div align="center">No Promotional Products Found!</div>
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
@include('footer')
    </body>
</html>
<script type="text/javascript">
$(document).ready(function() {
	$('#sortby').change(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		var cid = "{{ $catid }}";
		var bid = "{{ $bid }}";
		if(sortby != '' && showrecord != '') window.location = "{{ url('promotions/') }}"+"?sortby="+sortby+"&show="+showrecord+"&cid="+cid+"&bid="+bid;
	});
	$('#showrecord').change(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		var cid = "{{ $catid }}";
		var bid = "{{ $bid }}";
		if(sortby != '' && showrecord != '') window.location = "{{ url('promotions/') }}"+"?sortby="+sortby+"&show="+showrecord+"&cid="+cid+"&bid="+bid;
	});

	$('#loadmore').click(function() {
		var sortby = $('#sortby').val();
		var showrecord = $('#showrecord').val();
		var cid = "{{ $catid }}";
		var bid = "{{ $bid }}";
		var pagenum = $('#pagenum').val();
		pagenum = parseInt(pagenum) + 1;
		$('#pagenum').val(pagenum);
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("getproductslist") }}',				
			data: {'page': pagenum, 'IsPromotion': '1', 'cid': cid, 'bid': bid, 'sortby': sortby, 'show': showrecord, 'searchkey': ''},
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
	var cid = "{{ $catid }}";
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
	window.location = "{{ url('/promotions/') }}"+"?sortby="+sortby+"&show="+showrecord+"&cid="+cid+"&bid="+bid;
}

/*function setcategory(catid) {
	var sortby = $('#sortby').val();
	var showrecord = $('#showrecord').val();	
	var bid = "{{ $bid }}";
	if(sortby != '' && showrecord != '') {
		window.location = "{{ url('/promotions/') }}"+"?sortby="+sortby+"&show="+showrecord+"&cid="+catid+"&bid="+bid;
	}
}*/

function setcategory(catagoryid) {
	var sortby = $('#sortby').val();
	var showrecord = $('#showrecord').val();	
	var bid = "{{ $bid }}";
	var catid = "{{ $catid }}";
		
	var isChecked = $("#category"+catagoryid).is(":checked");
	if(isChecked) {
		if(catid != '') {
			catid = catid + "," + catagoryid;
		} else {
			catid = catagoryid;
		}
	} else {
		var cids = catid.split(',');
		cids = $.grep(cids, function(value) {
		  return value != catagoryid;
		});
		catid = cids.join(',');
	}
	
	//if(sortby != '' && showrecord != '') {
		window.location = "{{ url('/promotions/') }}"+"?sortby="+sortby+"&show="+showrecord+"&cid="+catid+"&bid="+bid;
	//}
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