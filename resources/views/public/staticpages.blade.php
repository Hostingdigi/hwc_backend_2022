@include('header')
<div class="container">
	@if(count($bannerads) > 0)
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@php 
					$x = 0;
				@endphp
				@foreach($bannerads as $adbanner)
					<div class="add-box" @if($x > 0) style="margin-top:10px;" @endif>
						@if($adbanner->Video != '')
							{!! $adbanner->Video !!}
						@else
							<a href="{{ $adbanner->ban_link }}"><img src="{{ url('/uploads/bannerads/'.$adbanner->	EnBanimage)}}" alt="{{ $adbanner->ban_name }}" class="img-fluid"></a>
						@endif
					</div>
					@php
						++$x;
					@endphp
				@endforeach
			</div>
			<div class="col-md-9 col-sm-12">
				<div class="row">
					<div class="col-12 section-title mt-5 mb-3">
						<h2>{{ $staticpage->EnTitle }}</h2>
					</div>
				</div>
				<div class="row justify-content-center">		
					<div class="col-md-12">
					{!! $staticpage->ChContent !!}
					</div>
				</div>
			</div>
		</div>
	@else	
	<div class="row">
	<div class="col-12 section-title mt-5 mb-3">
		<h2>{{ $staticpage->EnTitle }}</h2>
	</div>
	</div>
	<div class="row justify-content-center">		
		<div class="col-md-12">
		{!! $staticpage->ChContent !!}
		</div>
	</div>
	@endif
</div>
@include('footer')
    </body>
</html>

