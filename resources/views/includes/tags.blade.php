<div class="pro-tag">
	<div class="sec-title">
		<h6>Product Tag</h6>
	</div>
	<div class="tag-box">
		@if($tags->isEmpty())
		<ai>No tag found.</a>
		@endif
		@foreach($tags as $t)
		<a href="javascript:void(0);">{{ ucwords(strtolower(trim($t))) }}</a>
		@endforeach                                
	</div>
</div>