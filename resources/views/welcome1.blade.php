@include('header')

    

    @include('banner')

	<!-- letest product section -->
	<section class="top-letest-product-section mt-5">
		<div class="container">
			<div class="col-12 section-title">
				<h2>Promotion Items</h2>
			</div>
			<div class="card-deck">
				<div class="col-lg-4 col-md-6 col-sm-12">
			  <div class=" card">
				<img src="{{ asset('img/product/product-01.jpg') }}" class="card-img-top" alt="...">
				<div class="card-body">
				<div class="box"><img src="{{ asset('img/box.svg') }}" class="card-img-top" alt="..." >
        <span class="boxAdjest">50% OFF</span></div>
				  <h5 class="card-title text-center">Imalent Global Rofis R4</h5>
				  <div class="row">
					<div class="col-6">
						<div class="newrate">$179.00</div>
					</div>
					<div class="col-6">
						<div class="oldrate">$185.00</div>
					</div>
				  </div>
				  
				  <div class="text-center mt-3"><a href="#" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				</div>
			  </div>
			  </div>
			  
			  <div class="col-lg-4 col-md-6 col-sm-12">
			  <div class=" card">
				
				<img src="{{ asset('img/product/product-02.jpg') }}" class="card-img-top" alt="...">
				<div class="card-body">
					<div class="heartIcon"><a href="#">
          <i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px;
    color: #999; font-size: 21px;"></i></a></div>	
				  <h5 class="card-title text-center">Aerogaz S600 Instant Water Heater</h5>
				  <div class="row">
					<div class="col-6">
						<div class="newrate">$155.00</div>
					</div>
					<div class="col-6">
						<div class="oldrate">$170.00</div>
					</div>
				  </div>
				  <div class="text-center mt-3"><a href="#" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				</div>
			  </div>
			  </div>
			  <div class="col-lg-4 col-md-6 col-sm-12">
			  <div class="card ">
				<img src="{{ asset('img/product/product-03.jpg') }}" class="card-img-top" alt="...">
				<div class="card-body">
				<div class="heartIcon"><a href="#"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px;
    color: #999; font-size: 21px;"></i></a></div>
				  <h5 class="card-title text-center">Yellowyellow Water-Stop Cement</h5>
				  <div class="row">
					<div class="col-6">
						<div class="newrate">$10.50</div>
					</div>
					<div class="col-6">
						<div class="oldrate">$15.00</div>
					</div>
				  </div>
				  <div class="text-center mt-3"><a href="#" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				</div>
			  </div>
			  </div>
</div>

<div class="card-deck" style="margin-top:30px;">
			<div class="col-lg-4 col-md-6 col-sm-12">
			  <div class=" card">
				<img src="{{ asset('img/product/product-04.jpg') }}" class="card-img-top" alt="...">
				<div class="card-body">
				<div class="heartIcon"><a href="#">
        <i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px;
    color: #999; font-size: 21px;"></i></a></div>
				  <h5 class="card-title text-center">Sentery Safe STW123GDC</h5>
				  <div class="row">
					<div class="col-6">
						<div class="newrate">$1032.70</div>
					</div>
					<div class="col-6">
						<div class="oldrate">$1202.50</div>
					</div>
				  </div>
				  <div class="text-center mt-3"><a href="#" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				</div>
			  </div>
			  </div>
			  <div class="col-lg-4 col-md-6 col-sm-12">
			  <div class=" card">
				<img src="{{ asset('img/product/product-05.jpg') }}" class="card-img-top" alt="...">
				<div class="card-body">
				<div class="box">
        <img src="{{ asset('img/box.svg') }}" class="card-img-top" alt="..." >
        <span class="boxAdjest">35% OFF</span></div>
				  <h5 class="card-title text-center">Light Vault Magnus</h5>
				  <div class="row">
					<div class="col-6">
						<div class="newrate">$135.00</div>
					</div>
					<div class="col-6">
						<div class="oldrate">$175.00</div>
					</div>
				  </div>
				  <div class="text-center mt-3"><a href="#" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				</div>
			  </div>
			  </div>
			  <div class="col-lg-4 col-md-6 col-sm-12">
			  <div class=" card">
				<img src="{{ asset('img/product/product-06.jpg') }}" class="card-img-top" alt="...">
				<div class="card-body">
				<div class="heartIcon">
        <a href="#"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px;
    color: #999; font-size: 21px;"></i></a></div>
				  <h5 class="card-title text-center">Petronas Mercedes Team Tee Shirt</h5>
				  <div class="row">
					<div class="col-6">
						<div class="newrate">$99.00</div>
					</div>
					<div class="col-6">
						<div class="oldrate">$110.00</div>
					</div>
				  </div>
				  <div class="text-center mt-3"><a href="#" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a></div>
				</div>
			  </div>
			  </div>
</div>
<div class="text-center mt-5"><a href="#" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL</a></div>
		</div>
	</section>

  <section class="add-section">
<div class="container my-4 mb-5">







    <!--Carousel Wrapper-->
    <div id="multi-item-example" class="carousel slide carousel-multi-item d-none d-md-block" data-ride="carousel">

      <!--Controls-->
      <div class="controls-top">
        <a class="btn-floating prevss" href="#multi-item-example" data-slide="prev"><i class="fa fa-chevron-left" style="color:#000; position:relative; left:-45px;"></i></a>
        <a class="btn-floating nextss" href="#multi-item-example" data-slide="next"><i class="fa fa-chevron-right" style="color:#000; position:relative; right:-45px;"></i></a>
      </div>
      <!--/.Controls-->

      <!--Slides-->
      <div class="carousel-inner mb-5" role="listbox">

        <!--First slide-->
        <div class="carousel-item active mb-5">

          <div class="row">
            <div class="col-md-3  col-xs-3">
              <div class="card mb-2">
                <img class="card-img-top" src="{{ asset('img/ads/ad_01.jpg') }}" alt="Card image cap">                
              </div>
            </div>

            <div class="col-md-6  col-xs-6 clearfix d-none d-md-block">
              <div class="card mb-2">
                <div class="embed-responsive embed-responsive-16by9">
				 <iframe width="560" height="315" src="https://www.youtube.com/embed/C9I-W1eTCbk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>               
              </div>
            </div>

            <div class="col-md-3  col-xs-3 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="{{ asset('img/ads/ad_03.jpg') }}" alt="Card image cap">              
              </div>
            </div>
          </div>

        </div>
        <!--/.First slide-->

        <!--Second slide-->
        <div class="carousel-item mb-5">

          <div class="row">
            <div class="col-md-3 col-xs-3">
              <div class="card mb-2">
                <img class="card-img-top" src="{{ asset('img/ads/ad_04.jpg') }}" alt="Card image cap">
              </div>
            </div>

            <div class="col-md-6  col-xs-6 clearfix d-none d-md-block">
              <div class="card mb-2">
                <div class="embed-responsive embed-responsive-16by9">
				 <iframe width="560" height="315" src="https://www.youtube.com/embed/7m16dFI1AF8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>               
              </div>
            </div>

            <div class="col-md-3  col-xs-3 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="{{ asset('img/ads/ad_05.jpg') }} alt="Card image cap">
                
              </div>
            </div>
          </div>

        </div>
        <!--/.Second slide-->

        

      </div>
      <!--/.Slides-->

    </div>
    <!--/.Carousel Wrapper-->

	<!--Carousel Wrapper for mobile-->
    <div id="carouselExampleControls" class="carousel slide d-block d-md-none" data-ride="carousel">
	  <div class="carousel-inner">
	    <div class="carousel-item active">
	      <img class="d-block w-100" src="{{ asset('img/ads/ad_01.jpg') }}" alt="First slide">
	    </div>
	    <div class="carousel-item">
	      <div class="embed-responsive embed-responsive-16by9">
			 <iframe width="560" height="315" src="https://www.youtube.com/embed/C9I-W1eTCbk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div> 
	    </div>
	    <div class="carousel-item">
	      <img class="d-block w-100" src="{{ asset('img/ads/ad_03.jpg') }}" alt="Third slide">
	    </div>

	    <div class="carousel-item">
	      <img class="d-block w-100" src="{{ asset('img/ads/ad_04.jpg') }}" alt="Third slide">
	    </div>
	    <div class="carousel-item">
	      <div class="embed-responsive embed-responsive-16by9">
			 <iframe width="560" height="315" src="https://www.youtube.com/embed/7m16dFI1AF8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div> 
	    </div>
	    <div class="carousel-item">
	      <img class="d-block w-100" src="{{ asset('img/ads/ad_05.jpg') }}" alt="Third slide">
	    </div>
	  </div>
	  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
	    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
	    <span class="sr-only">Previous</span>
	  </a>
	  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
	    <span class="carousel-control-next-icon" aria-hidden="true"></span>
	    <span class="sr-only">Next</span>
	  </a>
	</div>
	<!--/.Carousel Wrapper for mobile-->


  </div>
</section>

@php
$homecategories = \App\Models\Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->skip(0)->take(8)->get();
@endphp

<section class="mb-5 mt-5">
	<div class="container">
		<div class="section-title mb-4">
			<h2>Categories</h2>
		</div>
		@if($homecategories)
			@php $hc = 0; @endphp
			<div class="row">
			@foreach($homecategories as $homecategory)
				@php $productscount = 0; @endphp
				@php $catproducts = \App\Models\Product::where('Types', '=', $homecategory->TypeId)->where('ProdStatus', '=', '1')->count(); @endphp
				@php $productscount = $productscount + $catproducts; @endphp
				@php
					$subcats = [];
					$subcats = \App\Models\Category::where('ParentLevel','=',$homecategory->TypeId)->orderBy('EnName', 'ASC')->get();
				@endphp	
				@if(count($subcats) > 0)
					@foreach($subcats as $subcat)
						@php 
							$subcatproducts = \App\Models\Product::where('Types', '=', $subcat->TypeId)->where('ProdStatus', '=', '1')->count();
							$productscount = $productscount + $subcatproducts;							
							$subsubcats = [];
							$subsubcats = \App\Models\Category::where('ParentLevel','=',$subcat->TypeId)->orderBy('EnName', 'ASC')->get();
						@endphp	
						@if(count($subsubcats) > 0)
							@foreach($subsubcats as $subsubcat)
								@php 
									$subsubcatproducts = \App\Models\Product::where('Types', '=', $subsubcat->TypeId)->where('ProdStatus', '=', '1')->count();
									$productscount = $productscount + $subsubcatproducts;
								@endphp
							@endforeach
						@endif						
					@endforeach
				@endif
				@if($hc % 4 == 0) </div><div class="row"> @endif
				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="">
						<img src="{{ url('/uploads/category/'.$homecategory->Image) }}" alt="{{ $homecategory->EnName }}" style="width:100%">
						<div class="caption">
							<h4 class="pt-3 pb-1">{{ $homecategory->EnName }}</h4>
							<p>{{ $catproducts }} Products</p>
						</div>
					</div>
				</div>
				@php ++$hc; @endphp
			@endforeach
			</div>
		@endif
	</div>
	<div class="text-center mt-4 mb-4"><a href="#" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL</a></div>
</section>


<!-- Product imaes end -->

<!-- banner  start -->
@php
	$homeban1 = \App\Models\Bannerads::where('ban_status', '=', '1')->orderBy('display_order', 'asc')->skip(0)->take(1)->first();
@endphp
@if($homeban1)
<section>	
	<div><img src="{{ url('/uploads/bannerads/'.$homeban1->EnBanimage) }}" alt="{{ $homeban1->ban_name }}" style="width:100%;"></div>
</section>
@endif
<!-- banner  end -->
<section class="add-section">
<div class="container my-4 mb-5 newbox">
	<div class="section-title mb-4">
		<h2 class="text-center">Brands</h2>
		<hr style="width:50px; border-bottom:4px solid #000;"></hr>
	</div>
	@php
	$homebrands = \App\Models\Brand::where('BrandStatus', '=', '1')->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->skip(0)->take(18)->get();
	@endphp
	
	@if($homebrands)
		@php $hb = 0; @endphp
		<div class="row">
		@foreach($homebrands as $homebrand)	
			@if($hb % 6 == 0) </div><div class="row mt-4"> @endif
			<div class="col-lg-2 col-md-4 col-sm-6 col-6">		
				<img src="{{ url('/uploads/brands/'.$homebrand->Image) }}" alt="{{ $homebrand->EnName }}" style="width:100%; margin:0px 0px 21px 0px;">
			</div>
			@php ++$hb; @endphp
		@endforeach
		</div>
	@endif
</div>
<div class="text-center pb-5 "><a href="#" class="site-btn2 btn-lg bg-dark mx-auto textyellow largebutton">VIEW ALL</a></div>
</section>

@php
	$homeban2 = \App\Models\Bannerads::where('ban_status', '=', '1')->orderBy('display_order', 'asc')->skip(1)->take(1)->first();
@endphp
@if($homeban2)
<section>	
	<div><img src="{{ url('/uploads/bannerads/'.$homeban2->EnBanimage) }}" alt="{{ $homeban2->ban_name }}" style="width:100%;"></div>
</section>
@endif
<!-- About section start-->
<section class="mt-5 mb-5">
<div class="container">
	<div class="row">
		@php
			$welcomecontent = \App\Models\PageContent::where('parent_id', '=', '12')->where('display_status', '=', '1')->first();
		@endphp
		<div class="col-md-4">
			<div>
			@if($welcomecontent->banner_image != '')
				<img src="{{ url('/uploads/pagecontent/'.$welcomecontent->banner_image) }}" alt="" style="width:100%;">
			@else
				<img src="{{ asset('img/ads/about.jpg') }}" alt="" style="width:100%;">
			@endif
			</div>
		</div>
		<div class="col-md-8">
			<div class="mt-2">
				<h2>About<br>HardwareCity®</h2>
				<hr style="width:50px; float:left; border-bottom:4px solid #000;"></hr>
			</div>
			<div style="clear:both;">
			
			
			@if($welcomecontent)
			{!! $welcomecontent->ChContent !!}
			@endif	
			
			</div>
		</div>
	</div>
</div>
</section>


  @include('footer')
    </body>
</html>
