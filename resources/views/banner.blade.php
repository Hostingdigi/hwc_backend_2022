@php $promotions = \App\Models\Promotions::where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get(); @endphp@if(count($promotions) > 0)
	<section id="hero">
		<div id="heroCarousel" class="carousel slide carousel-fade" data-ride="carousel">
			<ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
				<div class="carousel-inner" role="listbox">
					@php
						$x = 1;
					@endphp
					@foreach($promotions as $promotion)
						<!-- Slide 1 -->
						@php
							if($promotion->ban_link !='' && $promotion->ban_link !='#'){
								$link = $promotion->ban_link;
								$target = "_blank";
								}
							else{
								$link = "#";
								$target = "";
								}
						@endphp
						
						<div class="carousel-item @if($x == 1) active @endif" style="background-image: url({{ url('/uploads/promotionsbanner/'.str_replace(' ', '%20', $promotion->EnBanimage)) }})">
							<a href="{{$link}}" target="{{$target}}">
								<div class="carousel-container">
									<!--<div class="container">
										<div class="animate__animated animate__fadeInDown text-center banner1"><img src="{{ url('img/banner/24hr.png') }}" alt="" class="" style=" margin-top:-100px;"></div>
										<h2 class="animate__animated animate__fadeInUp text-center mx-auto">{{ $promotion->ban_name }}</h2>
										@if($promotion->ban_caption)									
											<h4 class="animate__animated animate__fadeInUp text-center mx-auto text-white">{!! $promotion->ban_caption !!}</h4>									
										@endif
										<a href="{{ $promotion->ban_link }}" class="btn-get-started animate__animated animate__fadeInUp scrollto mx-auto text-center bannerButton{{ $x }}">SHOP NOW</a>
									</div> -->
								</div>
							</a>
						</div>
						
						@php
							++$x;
						@endphp
					@endforeach
				</div>
				<a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
				<span ><img src="{{ url('img/back.png') }}" alt=""></span></a>
				<a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
				<span ><img src="{{ url('img/next.png') }}" alt=""></span></a>
		</div>
	</section>
	<!-- End Hero -->
@endif