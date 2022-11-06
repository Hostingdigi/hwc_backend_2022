@include('header')

<!-- Header section end -->
	<section id="breadcrumbs" class="breadcrumbs">
      <div class="container">

        <ol>
          <li><a href="index.html">Home</a></li>
		  <li><div class="pageheading">Brands</div></li>
        </ol>
      </div>
    </section>
	<!-- letest product section -->
	
	<!-- letest product section end -->
	
	<!-- Category Area -->
        <section class="all_brands_main">
		<div class="container container-mobile">
			<div class="row">
				<div class="col-md-12 col-lg-12 col-mobile">

					<div class="yCmsComponent span-24 section1 cms_disp-img_slot">
						<h1>List of Brands</h1>
		  		
						<form class="header-search-form" action="{{ url('/brands') }}" method="post">
							@csrf
							<div class="search_brands">
							<input type="text" placeholder="Find brand" name="brandname" id="brandname" value="{{ $brandname }}">
							<button type="submit"><i class="flaticon-search"></i><span  style="font-size: 14px; position: relative; top: -4px; padding-right: 4px;">Search</span></button>
							</div>
						</form>			  			
			  		</div>

				</div>
				<div class="listing_alpha">
					<div class="yCmsComponent span-24 section1 cms_disp-img_slot">
						<div class="listing_letter" id="scrollbar2">
							<div class="viewport">
								<div class="overview">
									<a href="{{ url('/brands?filter=0-9') }}">0-9</a>
									<a href="{{ url('/brands?filter=A') }}">A</a>
									<a href="{{ url('/brands?filter=B') }}">B</a>
									<a href="{{ url('/brands?filter=C') }}">C</a>
									<a href="{{ url('/brands?filter=D') }}">D</a>
									<a href="{{ url('/brands?filter=E') }}">E</a>
									<a href="{{ url('/brands?filter=F') }}">F</a>
									<a href="{{ url('/brands?filter=G') }}">G</a>
									<a href="{{ url('/brands?filter=H') }}">H</a>
									<a href="{{ url('/brands?filter=I') }}">I</a>
									<a href="{{ url('/brands?filter=J') }}">J</a>
									<a href="{{ url('/brands?filter=K') }}">K</a>
									<a href="{{ url('/brands?filter=L') }}">L</a>
									<a href="{{ url('/brands?filter=M') }}">M</a>
									<a href="{{ url('/brands?filter=N') }}">N</a>
									<a href="{{ url('/brands?filter=O') }}">O</a>
									<a href="{{ url('/brands?filter=P') }}">P</a>
									<a href="{{ url('/brands?filter=Q') }}">Q</a>
									<a href="{{ url('/brands?filter=R') }}">R</a>
									<a href="{{ url('/brands?filter=S') }}">S</a>
									<a href="{{ url('/brands?filter=T') }}">T</a>
									<a href="{{ url('/brands?filter=U') }}">U</a>
									<a href="{{ url('/brands?filter=V') }}">V</a>
									<a href="{{ url('/brands?filter=W') }}">W</a>
									<a href="{{ url('/brands?filter=X') }}">X</a>
									<a href="{{ url('/brands?filter=Y') }}">Y</a>
									<a href="{{ url('/brands?filter=Z') }}">Z</a>
									<a href="{{ url('/brands?filter=All') }}" class="all_view selected">ALL</a>
								</div>
							</div>							
						</div>
						@php 
							$alphas = ['0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'All'];
							if($filter != '' && $filter != 'All') {
								$alphas = [$filter];
							}
							$b = 1;
						@endphp
						<div class="letter_div">
							<dl class="split-list">
								<dl class="sub-list">
									@php
										$rowcount = 1;
									@endphp
									@foreach($alphas as $alpha)
										@php
											
											$brands = [];
											if($filter == '0-9' || $alpha == '0-9') {
												$brands = \App\Models\Brand::where('BrandStatus', '=', '1')->where('EnName', 'LIKE', '0%')->orWhere('EnName', 'LIKE', '1%')->orWhere('EnName', 'LIKE', '2%')->orWhere('EnName', 'LIKE', '3%')->orWhere('EnName', 'LIKE', '4%')->orWhere('EnName', 'LIKE', '5%')->orWhere('EnName', 'LIKE', '6%')->orWhere('EnName', 'LIKE', '7%')->orWhere('EnName', 'LIKE', '8%')->orWhere('EnName', 'LIKE', '9%')->orderBy('EnName', 'ASC')->get();											
											} else {
												if($brandname != '') {
													$brands = \App\Models\Brand::where('BrandStatus', '=', '1')->where('EnName', 'LIKE', $alpha.'%')->where('EnName', 'LIKE', '%'.$brandname.'%')->orderBy('EnName', 'ASC')->get();
												} else {
													$brands = \App\Models\Brand::where('BrandStatus', '=', '1')->where('EnName', 'LIKE', $alpha.'%')->orderBy('EnName', 'ASC')->get();
												}
											}												
										@endphp
										
										@if(count($brands) > 0)
											<dd data-id="{{ $alpha }}">
												<ul>
												<li class="list_head">{{ $alpha }}</li>
												@foreach($brands as $brand)
													<li><a href="{{ url('/brand/'.$brand->UniqueKey) }}">{{ $brand->EnName }}</a></li>
												@endforeach
												</ul>
											</dd>
										@endif	
										@if($rowcount == 1)
											@if($b % 5 == 0)												
												</dl><dl class="sub-list">
												@php ++$rowcount; $b = 1; @endphp
											@endif											
										@elseif($rowcount == 2)
											@if($b % 8 == 0)												
												</dl><dl class="sub-list">
												@php ++$rowcount; $b = 1; @endphp
											@endif
										@elseif($rowcount == 3) 
											@if($b % 6 == 0)												
												</dl><dl class="sub-list">
												@php ++$rowcount; $b = 1; @endphp
											@endif	
										@else
											@if($b % 5 == 0)												
												</dl><dl class="sub-list">
												@php ++$rowcount; $b = 1; @endphp
											@endif	
										@endif	
										@php
											++$b;											
										@endphp
									@endforeach									
								</dl>
							</dl>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Category Area -->

@include('footer')
   
