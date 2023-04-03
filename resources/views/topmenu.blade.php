<!-- Header section -->
<header class="header-section">
		<div class="header-top">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 text-center text-lg-left logoAdj">
						<!-- logo -->
						<a href="{{ url('/') }}" class="site-logo">
							<img src="{{ asset('front/img/logo.png') }}" alt="" class="">
						</a>
					</div>
					<div class="col-xl-6 col-lg-5">
						<form class="header-search-form" action="{{ url('/search') }}" method="post">
							<input type="text" placeholder="Search on HardwareCity ...." name="searchkey" required @if(isset($searchkey)) value="{{ $searchkey }}" @endif>
							<button type="submit"><i class="flaticon-search"></i><span  style="font-size: 14px; padding-left: 5px;  position: relative; top: -4px; padding-right: 4px;">SEARCH</span></button>
						</form>
					</div>
					<div class="col-xl-2 col-lg-5 pr-0 usAdj">
						<div class="user-panel">
							<div class="topicons">
								@php
									$cartitemcount = 0;
								@endphp
								@if(Session::has('cartdata'))		
									@php 
										$sessid = $cartitems = '';
										$sessid = Session::get('_token'); 
										$cartitems = Session::get('cartdata');
										if(count($cartitems)) {
											$cartitemcount = count($cartitems[$sessid]);
										}
										
									@endphp									
								@endif
								
								<span class="badge badge-pill black" id="cartcount">@if($cartitemcount > 0){{ $cartitemcount }}@endif</span>
								
								<a href="{{ url('/cart') }}"><i class="fa fa-shopping-cart shopppingcartIcon" aria-hidden="true" ></i></a>
							</div>
							<div class="topicons">
								@if(Session::has('customer_id') && Session::has('customer_name'))
									<a href="{{ url('/customer/favouriteproducts') }}">
								@else
									<a href="{{ url('/login') }}">
								@endif
								<i class="fa fa-heart-o heartIcontop" aria-hidden="true" ></i></a>
							</div>
                            
							<div class="topicons">
                                @if(Session::has('customer_id') && Session::has('customer_name'))
									<a href="{{ url('/customer/myorders') }}" class="text-sm text-gray-700 underline">
								@else
									<a href="{{ url('/login') }}" class="text-sm text-gray-700 underline">
								@endif
                                <i class="fa fa-user userIcontop" aria-hidden="true"></i></a>								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fulid" style="background-color:#000;">
			<div class="row topSocial">
						<div class="col-xl-2 col-lg-5 pr-0 usAdj-hide">
						<div class="user-panel">
							<div class="topicons">
								@php
									$cartitemcount = 0;
								@endphp
								@if(Session::has('cartdata'))		
									@php 
										$sessid = $cartitems = '';
										$sessid = Session::get('_token'); 
										$cartitems = Session::get('cartdata');
										if(count($cartitems)) {
											$cartitemcount = count($cartitems[$sessid]);
										}
										
									@endphp									
								@endif
								
								<span class="badge badge-pill mobile black" id="mcartcount">{{ $cartitemcount }}</span>
								<a href="{{ url('/cart') }}"><i class="fa fa-shopping-cart shopppingcartIcon" aria-hidden="true" ></i></a>
							</div>
							<div class="topicons">
								@if(Session::has('customer_id') && Session::has('customer_name'))
									<a href="{{ url('/customer/favouriteproducts') }}">
								@else
									<a href="{{ url('/login') }}">
								@endif
								<i class="fa fa-heart-o heartIcontop" aria-hidden="true" ></i></a>
							</div>
							<div class="topicons">
								@if(Session::has('customer_id') && Session::has('customer_name'))
									<a href="{{ url('/customer/myorders') }}" class="text-sm text-gray-700 underline">
								@else
									<a href="{{ url('/login') }}" class="text-sm text-gray-700 underline">
								@endif
								<i class="fa fa-user userIcontop" aria-hidden="true"></i></a>
							</div>
							<div class="topicons hidedesk">								
								<a href="tel:{{ $settings->company_phone }}" class="text-sm text-gray-700 underline">
								<i class="fa fa-phone-square userIcontop" aria-hidden="true"></i></a>
							</div>
							<div class="topicons hidedesk">								
								<a href="mailto:{{ $settings->admin_email }}" class="text-sm text-gray-700 underline">
								<i class="fa fa-envelope userIcontop" aria-hidden="true"></i></a>
							</div>
						</div>
					</div>
					</div>			
			<div class="row hidemob">
				<div class="container">
					<div class="row">
						<div class="col-12">							
							<div class="usernewtext">							
							@if(Session::has('customer_name'))
								Hi {{ Session::get('customer_name') }}!
							@else
								Hi Guest!
							@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<nav class="main-navbar" id="mainnavbar">
			<div class="container">
				<!-- menu -->
				<div class="row">
				<div class="col-12">
				<ul class="main-menu">					
					@php
						$menus = \App\Models\PageContent::whereRaw('menu_type = 2 or menu_type = 4')->where('parent_id', '=', '0')->where('display_status', '=', '1')->orderBy('display_order', 'asc')->get();
					@endphp
					@if($menus)
						@foreach($menus as $menu)
							@if($menu->UniqueKey == 'types')
								<li><a href="#" id="by_category_nav" style="text-transform:uppercase;">{{ $menu->EnTitle }} <i class="fa fa-caret-down" aria-hidden="true" style="color:#ffff00"></i></a></li>
							@elseif($menu->UniqueKey == 'brands')
								<li><a href="#" id="by_brand_nav" style="text-transform:uppercase;">{{ $menu->EnTitle }} <i class="fa fa-caret-down" aria-hidden="true" style="color:#ffff00"></i></a></li>
							@else
								<li><a href="{{ url('/'.$menu->UniqueKey) }}" style="text-transform:uppercase;">{{ $menu->EnTitle }}</a></li>
							@endif
						@endforeach
					@endif	
				</ul>
				</div>
				
				</div>
			</div>
		</nav>
	</header>
	<!-- Header section end -->
	
	<div id="feedback-link" class="" role="button" tabindex="0" style="margin: auto 1px 1px auto; padding: 0px; border-style: solid; border-width: 0px; font-style: normal; font-weight: normal; font-variant: normal; list-style: outside none none; letter-spacing: normal; line-height: normal; text-decoration: none; vertical-align: baseline; white-space: normal; word-spacing: normal; background-repeat: repeat-x; background-position: left bottom; background-color: transparent; border-color: transparent; width: 1px; height: 228px; cursor: pointer; display: block; z-index: 107158; position: fixed; inset: 0px 0px auto auto;"><a href="{{ url('/feedback') }}"><img src="{{ url('img/feedback_icon_img.png') }}" id="feedback" alt="Feedback" class="LPMimage" style="margin: 0px; padding: 0px; border-style: none; border-width: 0px; font-style: normal; font-weight: normal; font-variant: normal; list-style: outside none none; letter-spacing: normal; line-height: normal; text-decoration: none; vertical-align: baseline; white-space: normal; word-spacing: normal; position: absolute; top: 228px; left: -26px; z-index: 600;"></a></div>