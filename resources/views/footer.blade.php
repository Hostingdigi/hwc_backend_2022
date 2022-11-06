	@php
		$settings = \App\Models\Settings::first();
	@endphp	
    <!-- Footer section -->
	<section class="footer-section mt-5">
		<div class="container addBg">
			<div class="row " style="margin:0;">
				<div class="col-md-8" style="margin:0; padding:0;">
				<div class="emailbox"><span class="emailadj"><img src="{{ url('img/mail.png') }}" alt=""> {{ $settings->admin_email }}</span><img src="{{ url('img/banner/bg-content.png') }}" alt="" class="imagehide"></div>
				</div>
				<div class="col-md-4" style="margin:0; padding:0;">
					<div class="phonebox"><span class="phoneadj"><img src="{{ url('img/phone.png') }}" alt="">{{ $settings->company_phone }}</span></div>
				</div>
			</div>
		</div>
		<div class="container mb-5">
			<div class="row mt-5">				
				<div class="col-md-7">					
					<div class="row">
						@php							
							$footermenus = [];
							$footermenus = \App\Models\PageContent::where('menu_type', '=', '5')->where('display_status', '=', '1')->where('parent_id', '=', '0')->orderBy('display_order', 'asc')->get();
						@endphp
						
						@if($footermenus)																
							@foreach($footermenus as $footermenu)
							<div class="col-lg-4 col-sm-6">
								<div class="footer-widget about-widget">									
									@php							
										$footersubmenus = [];
										$footersubmenus = \App\Models\PageContent::where('display_status', '=', '1')->where('parent_id', '=', $footermenu->Id)->orderBy('display_order', 'asc')->get();
									@endphp
									@if(count($footersubmenus) > 0) 
										<h2>{{ $footermenu->EnTitle }}:</h2>
										<hr style="width:50px; float:left; border-bottom:4px solid #000;"></hr>
										<p style="clear:both;">
										@foreach($footersubmenus as $footersubmenu)
											<div><a href="{{ url('/'.$footersubmenu->UniqueKey) }}">{{ $footersubmenu->EnTitle }}</a></div>
										@endforeach
										</p>										
									@endif	
								</div>
							</div>
							@endforeach	
						@endif	
						
						</div>
					</div>
					
					<div class="col-md-5">
						<div class="row">
							<div class="col-lg-4 col-sm-6">
					<div class="footer-widget about-widget">
						<h2 class="find22">FIND US ON :</h2>
						<hr style="width:50px; float:left; border-bottom:4px solid #000;"></hr>
						<p style="clear:both;">
							@php
								$sociallinks = \App\Models\Socialmedia::where('status', '=', '1')->orderBy('display_order', 'asc')->get();
							@endphp
							@if($sociallinks)
								@foreach($sociallinks as $sociallink)
									@if($sociallink->id == 1)
										<div class="socialicons"><a href="{{ $sociallink->sociallink }}" target="_blank"><img src="{{ url('/images/fb.png') }}"></a></div>
									@endif
									@if($sociallink->id == 2)
										<div class="socialicons"><a href="{{ $sociallink->sociallink }}" target="_blank"><img src="{{ url('/images/insta.png') }}"></a></div>
									@endif
									@if($sociallink->id == 3)
										<div class="socialicons"><a href="{{ $sociallink->sociallink }}" target="_blank"><img src="{{ url('/images/linkedin.png') }}"></a></div>
									@endif
									@if($sociallink->id == 4)
										<div class="socialicons"><a href="{{ $sociallink->sociallink }}" target="_blank"><img src="{{ url('/images/youtube.png') }}"></a></div>
									@endif
									@if($sociallink->id == 5)
										<div class="socialicons"><a href="{{ $sociallink->sociallink }}" target="_blank"><i class="fa fa-pinterest"></i></a></div>
									@endif
									@if($sociallink->id == 6)
										<div class="socialicons"><a href="{{ $sociallink->sociallink }}" target="_blank"><i class="fa fa-google-plus"></i></a></div>
									@endif
								@endforeach
							@endif	
								
							<!--div><img src="{{ url('img/ari.jpg') }}" alt="Ariba Network" class="findus"></div-->
						</p>
					</div>
				</div>

				<div class="col-lg-8 col-sm-6">
					<div class="footer-widget about-widget">
						<h2>JOIN OUR MAILING LIST</h2>
						<hr style="width:50px; float:left; border-bottom:4px solid #000;"></hr>
						<p style="clear:both;">
							<div>Get our exclusive offers and promotions delivered to your inbox.</div>
							<div id="subscribemsg" style="display:none;"></div>
							<div class="input-group mt-3">
							  <input type="email" name="subscribe_email" id="subscribe_email" class="form-control" placeholder="Enter your email id" aria-label="Text input with dropdown button">
							  <div class="input-group-append">
								<button class="btn btn-outline-secondary textcolorbut bg-dark" id="subscribe" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Submit</button>
							  </div>
							</div>
							<div class="mt-3">We accept Payment by :</div>	
								<div class="cardpayment mt-3"><img src="{{ url('img/payments.png') }}" width="160px" alt=""></div>
						</p>
					</div>
				</div>
						</div>
					</div>
					
				</div>
		</div>
		<div class="social-links-warp bg-dark">
			<div class="container">
				<p class="text-white text-center mt-3">&copy;<script>document.write(new Date().getFullYear());</script> HardwareCity Singapore. All rights reserved.</p>
			<div class="mx-auto text-center text-white footerText mb-3">
				@php
					$bm = 1;
					$bottommenus = [];
					$bottommenus = \App\Models\PageContent::where('menu_type', '=', '6')->where('display_status', '=', '1')->orderBy('display_order', 'asc')->get();
				@endphp
				@if($bottommenus)
					@foreach($bottommenus as $bottommenu)
					<span><a href="{{ url('/'.$bottommenu->UniqueKey) }}" class="siteLink">{{ $bottommenu->EnTitle }}</a></span>@if($bm < count($bottommenus)) | @endif 
					@php ++$bm; @endphp
					@endforeach
				@endif					
			</div>
			</div>
		</div>
	</section>
    @include('sidenav')
    <!--====== Javascripts & Jquery ======-->
	<script src="{{ asset('front/js/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ asset('front/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('front/js/jquery.slicknav.min.js') }}"></script>
	<script src="{{ asset('front/js/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('front/js/jquery.nicescroll.min.js') }}"></script>
	<script src="{{ asset('front/js/jquery.zoom.min.js') }}"></script>
	<script src="{{ asset('front/js/jquery-ui.min.js') }}"></script>
	<script src="{{ asset('front/js/slinky.min.js') }}"></script>
	<script src="{{ asset('front/js/main.js') }}"></script>
	<script src="{{ asset('front/js/jquery.ticker.js') }}"></script>
	<script type="text/javascript">
    $(document).ready(function(){  
		$('#js-news').ticker();
		$('.alert').delay(3000).fadeOut('fast');
		$('.box-Added-product').delay(3000).fadeOut('fast');
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
	
	</body>
</html>