<div class="cat-brand">
	<div class="sec-title">
		<h6>Categories</h6>
	</div>
	<div class="brand-box">
		<ul class="list-unstyled">
		@if($categories->isEmpty())
			<li>No category found.</li>
		@endif
		@php 
			$categoryids = !empty($catid) ? explode(',', $catid) : []; 
		@endphp
		@foreach($categories as $c)
		<li>
			<input type="checkbox" id="category{{ $c->id }}" value="{{ $c->id }}" name="categories[]" class="brands" data-value="{{ $c->id }}" onchange="setcategory(this.value);" @if(in_array($c->id, $categoryids)) checked @endif>
			<label for="{{ucwords($c->name)}}"> {{ ucwords($c->name) }}</label>
		</li>
		@endforeach
		</ul>
	</div>
	</div> 