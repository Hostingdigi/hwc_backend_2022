<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	<title>{{ $meta['title'] }}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $meta['description'] ?? '' }}">
        <meta name="keywords" content="{{ $meta['keywords'] ?? '' }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    @if(app()->environment('production'))
        <meta name="google-site-verification" content="1FHXAhBf4KOaY7Dxw4WhWi5r3z8atGo7_QA-CDufOaQ" />
        @endif
        @if(!empty($productdetail) && Str::contains(request()->path(), 'prod/'))
        <meta property="og:title" content="{{ $productdetail->EnName }}" />
        <meta property="og:description" content="{{$productdetail->EnName}}" />
        <meta property="og:image" content="{{trim($productdetail->LargeImage)!='' ? url('uploads/product/'.$productdetail->LargeImage) : url('frontend/images/noimage.png')}}" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta property="og:type" content="website" />
        @endif

        <!-- Favicon -->
        <link href="{{ url('frontend/images/favicon.ico') }}" rel="shortcut icon"/>
        @if($meta['canonical'] == 1)
        <link href="{{ url('prod') }}/{{$meta['seo_url']}}" rel="canonical" />
        @endif
        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/flaticon.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/slicknav.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/jquery-ui.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/owl.carousel.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/slinky.min.css') }}" />		
        <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/style_product.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/banner.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/normalize.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/responsive.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/custom.css') }}" />
        <link rel="stylesheet" href="{{ asset('frontend/css/ticker-style.css') }}" />

	    @stack('child-css')

        @if(app()->environment('production'))
        <!--Start of Zendesk Chat Script-->
        <script type="text/javascript">
            window.$zopim||(function(d,s){var z=$zopim=function(c){
            z._.push(c)},$=z.s=
            d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
            _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
            $.src='https://v2.zopim.com/?6FIVDCXQheiWd7dUCPAWf1Dli5JgiPcD';z.t=+new Date;$.
            type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
        </script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script>
        window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'UA-142468295-1');
        </script>
        @endif

        @if($meta['seo_url'] == 'success')
            @php
                $billemail = '';
                if(isset($_REQUEST['orderid'])) {
                    $lastorderid = $_REQUEST['orderid'];
                    $ordermastertbl = \App\Models\OrderMaster::where('order_id', '=', $lastorderid)->select('order_id','payable_amount','bill_email')->first();
                    if($ordermastertbl) $billemail = $ordermastertbl->bill_email;
                }
            @endphp
        <script>
            var enhanced_conversion_data = {email : "{{$billemail}}"};
        </script>
        @endif
        
        @if(app()->environment('production'))
        <!-- Global site tag (gtag.js) - Google Ads: 762150370 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-762150370"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config','AW-762150370', {'allow_enhanced_conversions':true});
        </script>
        @endif

        @if($meta['seo_url'] == 'success')
        <!-- Event snippet for Purchase conversion page -->
        <script>
        gtag('event', 'conversion', {
            'send_to': 'AW-762150370/kTlHCKKpqrADEOL7tesC',
            'value' : '{{ $ordermastertbl->payable_amount }}',
            'currency': 'SGD',
            'transaction_id': ''
        });
        </script>
        @endif

        @if(app()->environment('production'))
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-P32L9TZQ');</script>
        <!-- End Google Tag Manager -->
        @endif
    </head>
    <body>
        @if(app()->environment('production'))
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P32L9TZQ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        @endif
        
        @include('includes.header')

        @yield('content')

        @include('includes.footer')

         <!-- Javascripts & Jquery -->
        <script src="{{ asset('frontend/js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery.slicknav.min.js') }}"></script>
        <script src="{{ asset('frontend/js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery.zoom.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('frontend/js/slinky.min.js') }}"></script>
        <script src="{{ asset('frontend/js/main.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery.ticker.js') }}"></script>
        <script src="{{ asset('frontend/js/webApiService.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function(){  
                $('#js-news').ticker();
                @if(!Request::is('stripe'))
                $('.alert').delay(3000).fadeOut('fast');
                @endif
                //$('.box-Added-product').delay(1000).fadeOut('fast');
                $('#subscribe_email').focus(function() {
                    $('#subscribe_email').css('border', '1px solid #ced4da');
                });
                $('#subscribe').click(function() {
                    var subscribeemail = $('#subscribe_email').val();
                    if(subscribeemail == '') {
                        $('#subscribe_email').css('border', '1px solid red');
                    } else {
                        if(!validateEmail(subscribeemail)) {
                            $('#subscribemsg').html('Invalid Email Format!');
                            $('#subscribemsg').css('color', 'red');
                            $('#subscribemsg').show();
                            $('#subscribemsg').delay(5000).fadeOut('fast');	
                        } else {
                        var token = "{!! csrf_token() !!}";
                            $.ajax({
                                type: "POST",
                                url: '{{ url("/") }}/newslettersubscribe',				
                                data: {'email': subscribeemail},
                                headers: {'X-CSRF-TOKEN': token},		
                                success: function(response) {
                                    console.log(response);
                                    if(response == 'Success') {	
                                        $('#subscribemsg').html('Thanks for your subscription!');
                                        $('#subscribemsg').css('color', 'green');
                                        $('#subscribemsg').show();
                                        $('#subscribemsg').delay(5000).fadeOut('fast');	
                                        $('#subscribe_email').val('');
                                    } else {
                                        $('#subscribemsg').html('Email Id already in subscribers list!');
                                        $('#subscribemsg').css('color', 'red');
                                        $('#subscribemsg').show();
                                        $('#subscribemsg').delay(5000).fadeOut('fast');	
                                        $('#subscribe_email').val('');
                                    }
                                },error: function(ts) {				
                                    console.log("Error:"+ts.responseText);  
                                }
                            });
                        }
                    }
                });
            });
            
            function validateEmail(email) {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                return emailReg.test(email);
            }
            
            window.onscroll = function() {stickymenu()};

            // Get the navbar
            var navbar = document.getElementById("mainnavbar");

            // Get the offset position of the navbar
            var sticky = navbar.offsetTop;

            // Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
            function stickymenu() {	
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky")
            } else {
                navbar.classList.remove("sticky");
            }
            }
            
            </script>
	    @stack('child-scripts')
	</body>
</html>