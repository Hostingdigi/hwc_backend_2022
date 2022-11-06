@php		
	$seo = \App\Models\PageContent::where('UniqueKey', '=', 'home')->select('meta_title', 'meta_keywords', 'meta_description')->first();
	use Illuminate\Support\Facades\Route;
	$actionname = Route::getCurrentRoute()->getActionName();
	$routename = Request::route()->getName();
	$currenturl = Request::url();
	$urlarray = [];
	$seourl = $metatitle = $metakey = $metadesc = '';
	$urlarray = @explode('/', $currenturl);
	if($urlarray) {
		$seourl = end($urlarray);
		$seourl = str_replace('%20', ' ', $seourl);
	}
	
	if($seo) {
		$metatitle = $seo->meta_title;
		$metakey = $seo->meta_keywords;
		$metadesc = $seo->meta_description;
	}
	
	if(Request::is('prod/*')) {		
		if($seourl) {			
			$seo = \App\Models\Product::where('UniqueKey', '=', $seourl)->select('MetaTitle', 'MetaKey', 'MetaDesc')->first();
			if($seo) {
				$metatitle = $seo->MetaTitle;
				$metakey = $seo->MetaKey;
				$metadesc = $seo->MetaDesc;
			}
		}		
	} else {
		if(Request::is('promotions')) {
			$seo = \App\Models\PageContent::where('UniqueKey', '=', 'promotions')->select('meta_title', 'meta_keywords', 'meta_description')->first();
		} elseif(Request::is('brand/*')) {		
			if($seourl) {			
				$seo = \App\Models\Brand::where('UniqueKey', '=', $seourl)->select('meta_title', 'meta_keywords', 'meta_description')->first();				
			}		
		} elseif(Request::is('type/*') || Request::is('category/*')) {		
			if($seourl) {			
				$seo = \App\Models\Category::where('UniqueKey', '=', $seourl)->select('meta_title', 'meta_keywords', 'meta_description')->first();
			}		
		} elseif(strpos($actionname, '@staticpages') !== false) {
			if($seourl) {
				$seo = \App\Models\PageContent::where('UniqueKey', '=', $seourl)->select('meta_title', 'meta_keywords', 'meta_description')->first();
			}
		}
		if($seo) {
			$metatitle = $seo->meta_title;
			$metakey = $seo->meta_keywords;
			$metadesc = $seo->meta_description;
		}
	}		
	
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<title>{{ $metatitle }}</title>
	<meta charset="UTF-8">
	<meta name="description" content="{{ $metadesc }}">
	<meta name="keywords" content="{{ $metakey }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Favicon -->
	<link href="{{ url('images/favicon.ico') }}" rel="shortcut icon"/>

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">


	<!-- Stylesheets -->
	<link rel="stylesheet" href="{{ asset('front/css/bootstrap.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/font-awesome.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/flaticon.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/slicknav.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/jquery-ui.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/owl.carousel.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/animate.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/slinky.min.css') }}"/>		
	<link rel="stylesheet" href="{{ asset('front/css/style.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/style_product.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/banner.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/normalize.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/responsive.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/custom.css') }}"/>
	<link rel="stylesheet" href="{{ asset('front/css/ticker-style.css') }}"/>
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head><body>
<div id="preloder--">
		<div class="loader--"></div>
	</div>
	<div class="bg-white topmenuadj" style="background-color:#e5e5e5!important;" >
		<div class="container">
			<div class="row">								
			@php
				$settings = \App\Models\Settings::first();
				$announcements = \App\Models\Announcements::where('status', '=', '1')->orderBy('display_order', 'asc')->get();
				$marquetxt = '';
			@endphp					
					<div class="col-md-8 col-sm-12">
						<div class="texttop2">
							@if(count($announcements) > 0)
								
								@foreach($announcements as $announcement)
									@php
										$marquetxt .= strip_tags($announcement->message);
									@endphp
								@endforeach
								<marquee direction="left" width="100%">
								{{ $marquetxt }}
								</marquee>
							@else	
							<i class="fa fa-truck" aria-hidden="true" style="color:#999;"></i>&nbsp Free delivery for orders above S$88.00. For international shipping select country at checkout.
							@endif
						</div>
					</div>
					<div class="col-md-4 col-sm-12 no-pm pad-35" >
						<div class="texttop hidemob"><a href="#" style="color:#000;"> <i class="fa fa-phone-square" aria-hidden="true" style="color:#999;"></i>&nbsp; {{ $settings->company_phone }}</a> &nbsp; |  &nbsp; <a href="mailto:{{ $settings->admin_email }}" style="color:#000;"> <i class="fa fa-envelope" aria-hidden="true" style="color:#999;"></i>&nbsp {{ $settings->admin_email }}</a></div>
					</div>
					</div>
		</div>
	</div>
	@include('topmenu')