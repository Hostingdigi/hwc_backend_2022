<div id="preloder--">
	<div class="loader--"></div>
</div>
<div class="bg-white topmenuadj" style="background-color:#e5e5e5!important;" >
	<div class="container">
		<div class="row">								
		@php
		@endphp					
			<div class="col-md-8 col-sm-12">
				<div class="texttop2">	
					@if(!empty($topBar['announcements']))
                    <marquee direction="left" width="100%">
                        {{ $topBar['announcements'] }}
                    </marquee>
                    @else
                    <i class="fa fa-truck" aria-hidden="true" style="color:#999;"></i>&nbsp Free delivery for orders above S$88.00. For international shipping select country at checkout.
                    @endif							
				</div>
			</div>
			@if($topBar['settings'])
			<div class="col-md-4 col-sm-12 no-pm pad-35" >
				<div class="texttop hidemob">
					<a href="javascript::(0);" style="color:#000;"><i class="fa fa-phone-square" aria-hidden="true" style="color:#999;"></i>&nbsp; {{ $topBar['settings']['company_phone'] }}</a> &nbsp; |  &nbsp; <a href="mailto:{{ $topBar['settings']['admin_email'] }}" style="color:#000;"> <i class="fa fa-envelope" aria-hidden="true" style="color:#999;"></i>&nbsp {{ $topBar['settings']['admin_email'] }}</a>
				</div>
			</div>
			@endif
		</div>
	</div>
</div>
@include('topmenu')