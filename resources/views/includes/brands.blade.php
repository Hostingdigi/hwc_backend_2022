<div class="cat-brand">
	<div class="sec-title">
		<h6>Brands</h6>
	</div>
	<div class="brand-box">
		<ul class="list-unstyled">
		@php
			$brandids = !empty($bid) ? explode(',', $bid) : [];
		@endphp
		@foreach($brands as $b)
			<li>
				<input type="checkbox" id="brand{{ $b->id }}" value="{{ $b->id }}" name="brands[]" class="brands" data-value="{{ $b->id }}" onchange="setbrand(this.value);" @if(in_array($b->id, $brandids)) checked @endif>
				<label for="{{ ucwords(strtolower(trim($b->name))) }}">{{ ucwords(strtolower(trim($b->name))) }}</label>
			</li>
		@endforeach
		</ul>
	</div>
</div>