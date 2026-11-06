@foreach($banners as $ak => $b)
<div class="add-box" @if($ak > 0) style="margin-top:10px;" @endif>
	@if($b->Video != '')
		{!! $b->Video !!}
	@else
	<a href="{{ $b->ban_link }}">
		<img loading="lazy" onerror="this.onerror=null;this.src='{{config('shop.fallback_img')}}';" src="{{ url('uploads/bannerads/'.$b->EnBanimage)}}" alt="{{ $b->ban_name }}" class="img-fluid">
	</a>
	@endif
</div>
@endforeach