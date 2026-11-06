@if($tags->isNotEmpty())
<div class="pro-tag">
	<div class="sec-title">
		<h6>Product Tag</h6>
	</div>
	<div class="tag-box">
		@foreach($tags as $t)
		<a href="javascript:void(0);">{{ ucwords(strtolower(trim($t->ProdTags))) }}</a>
		@endforeach                                
	</div>
</div>
@endif