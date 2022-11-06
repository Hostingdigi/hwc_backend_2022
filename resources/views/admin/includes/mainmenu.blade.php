@if(Session::has('admin_id') && Session::has('priority'))
    <!-- BEGIN: Main Menu-->
    <div class="horizontal-menu-wrapper">
        <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-without-dd-arrow navbar-shadow menu-border" role="navigation" data-menu="menu-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto"><a class="navbar-brand" href="#">
                            <div class="brand-logo"></div>
                            <h2 class="brand-text mb-0">HardwareCity</h2>
                        </a></li>
                    <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
                </ul>
            </div>
			
			@if(Session::has('admin_id') && Session::has('priority'))
				
				<!-- Horizontal menu content-->
				<div class="navbar-container main-menu-content" data-menu="menu-container">
					<!-- include ../../../includes/mixins-->
					<ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
						@if(Session::get('priority') == 0)
						<li class="dropdown nav-item" data-menu="dropdown"
						><a class="dropdown-toggle nav-link" href="# data-toggle="dropdown"><i class="feather icon-home"></i><span data-i18n="Dashboard">Content &amp; Masthead</span></a>
							<ul class="dropdown-menu">
								<li class="" data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/static_pages') }}" data-toggle="dropdown" data-i18n="Analytics">
								<i class="fa fa-newspaper-o" aria-hidden="true"></i>Page Content</a>
								</li>
								<li data-menu="">							
								<a class="dropdown-item" href="{{ url('/admin/banner_master') }}" data-toggle="dropdown" data-i18n="banner_master">
								<i class="fa fa-file-image-o" aria-hidden="true"></i>Masthead Images</a>
								<a class="dropdown-item" href="{{ url('/admin/promotions') }}" data-toggle="dropdown" data-i18n="promotions">
								<i class="fa fa-bandcamp" aria-hidden="true"></i>Home Banners</a>
								<a class="dropdown-item" href="{{ url('/admin/banner_ads') }}" data-toggle="dropdown" data-i18n="banner_ads">
								<i class="fa fa-tripadvisor" aria-hidden="true"></i>Advertisement Banners</a>
								<a class="dropdown-item" href="{{ url('/admin/announcement') }}" data-toggle="dropdown" data-i18n="announcement">
								<i class="fa fa-bullhorn" aria-hidden="true"></i>Announcement</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/emailtemplate') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-envelope-open-o" aria-hidden="true"></i>Email Template</a>
								</li>
								<!--li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/smstemplate') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-commenting-o" aria-hidden="true"></i>SMS Template</a>
								</li-->
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/socialmedia') }}" data-toggle="dropdown" data-i18n="Customer">
									<i class="fa fa-medium" aria-hidden="true"></i>Social Media Configuration</a>
								</li>
							</ul>
						</li>
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
						<i class="fa fa-user-o" aria-hidden="true"></i><span data-i18n="Apps">Manage Customer</span></a>
							<ul class="dropdown-menu">
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/customergroup') }}" data-toggle="dropdown" data-i18n="Groups">
								<i class="fa fa-user-circle" aria-hidden="true"></i>Customer Group</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/customer') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-users" aria-hidden="true"></i>Customer</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/couponcode') }}" data-toggle="dropdown" data-i18n="couponcode">
								<i class="fa fa-gift" aria-hidden="true"></i>Coupon Code</a>
								</li>
							</ul>
						</li>
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
						<i class="feather icon-package"></i><span data-i18n="Apps">Manage Products</span></a>
							<ul class="dropdown-menu">
							  
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/category') }}" data-toggle="dropdown" data-i18n="Groups">
								<i class="fa fa-certificate" aria-hidden="true"></i>Category</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/brands') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-sun-o" aria-hidden="true"></i>Brands</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/products') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-product-hunt" aria-hidden="true"></i>Products</a>
								</li>
							</ul>
						</li>
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
						<i class="fa fa-money" aria-hidden="true"></i><span data-i18n="Apps">Sales Order</span></a>
							<ul class="dropdown-menu">
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/orders') }}" data-toggle="dropdown" data-i18n="Groups">
							   <i class="fa fa-bell-o" aria-hidden="true"></i>Online Order</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/pendingorders') }}" data-toggle="dropdown" data-i18n="Groups">
							   <i class="fa fa-bell-o" aria-hidden="true"></i>Pending Orders / Quotations</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Customer">
								<i class="feather icon-message-square"></i>Request for Quote</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/archives') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-archive" aria-hidden="true"></i>Archives (Deleted Order)</a>
								</li>
							</ul>
						</li>
						<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/subscriber') }}">
						<i class="fa fa-envelope" aria-hidden="true"></i>
						<span data-i18n="Subscriber">Subscriber</span></a>
						</li>
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
					   <i class="fa fa-cog" aria-hidden="true"></i><span data-i18n="Settings">Settings</span></a>
							<ul class="dropdown-menu">
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/subadmin_settings') }}" data-toggle="dropdown" data-i18n="Groups">
								<i class="fa fa-sitemap" aria-hidden="true"></i>Site Configuration</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/payment_settings') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-snowflake-o" aria-hidden="true"></i>Payment Configuration</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/paymentmethods') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-credit-card-alt" aria-hidden="true"></i>Payment Methods</a>
								</li>							
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/shipping_methods') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-houzz" aria-hidden="true"></i>Delivery Types</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/country') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-medium" aria-hidden="true"></i>Shipping Countries</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/local_shipping_methods') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-asterisk" aria-hidden="true"></i>Local Shipping Methods</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/international_shipping_methods') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-plane" aria-hidden="true"></i>International Shipping Methods</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/roleandrights') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-sitemap" aria-hidden="true"></i>Role & Rights</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/manageadmin') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-address-card" aria-hidden="true"></i>Manage Admin</a>
								</li>
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/passwordchange') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-key" aria-hidden="true"></i>Change Password</a>
								</li>
							</ul>
						</li>
					@else
						@if(Session::has('accessrights'))
							@php
								$accessrights = Session::get('accessrights');
							@endphp
						@endif	
						@if(in_array('7', $accessrights) || in_array('8', $accessrights) || in_array('9', $accessrights) || in_array('10', $accessrights) || in_array('11', $accessrights) || in_array('12', $accessrights) || in_array('13', $accessrights) || in_array('14', $accessrights))
						<li class="dropdown nav-item" data-menu="dropdown"
						><a class="dropdown-toggle nav-link" href="# data-toggle="dropdown"><i class="feather icon-home"></i><span data-i18n="Dashboard">Content &amp; Masthead</span></a>
							<ul class="dropdown-menu">
								@if(in_array('7', $accessrights))
								<li class="" data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/static_pages') }}" data-toggle="dropdown" data-i18n="Analytics">
								<i class="fa fa-newspaper-o" aria-hidden="true"></i>Page Content</a>
								</li>
								@endif
								@if(in_array('8', $accessrights))
								<li data-menu="">							
								<a class="dropdown-item" href="{{ url('/admin/banner_master') }}" data-toggle="dropdown" data-i18n="banner_master">
								<i class="fa fa-file-image-o" aria-hidden="true"></i>Masthead Images</a>
								</li>
								@endif
								@if(in_array('9', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/promotions') }}" data-toggle="dropdown" data-i18n="promotions">
								<i class="fa fa-bandcamp" aria-hidden="true"></i>Home Banners</a>
								</li>
								@endif
								@if(in_array('10', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/banner_ads') }}" data-toggle="dropdown" data-i18n="banner_ads">
								<i class="fa fa-tripadvisor" aria-hidden="true"></i>Advertisement Banners</a>
								</li>
								@endif
								@if(in_array('11', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/announcement') }}" data-toggle="dropdown" data-i18n="announcement">
								<i class="fa fa-bullhorn" aria-hidden="true"></i>Announcement</a>
								</li>
								@endif
								@if(in_array('12', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/emailtemplate') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-envelope-open-o" aria-hidden="true"></i>Email Template</a>
								</li>
								@endif
								@if(in_array('13', $accessrights))
								<!--li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/smstemplate') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-commenting-o" aria-hidden="true"></i>SMS Template</a>
								</li-->
								@endif
								@if(in_array('14', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/socialmedia') }}" data-toggle="dropdown" data-i18n="Customer">
									<i class="fa fa-medium" aria-hidden="true"></i>Social Media Configuration</a>
								</li>
								@endif
							</ul>
						</li>
						@endif
						@if(in_array('15', $accessrights) || in_array('16', $accessrights) || in_array('17', $accessrights))
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
						<i class="fa fa-user-o" aria-hidden="true"></i><span data-i18n="Apps">Manage Customer</span></a>
							<ul class="dropdown-menu">
								@if(in_array('15', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/customergroup') }}" data-toggle="dropdown" data-i18n="Groups">
								<i class="fa fa-user-circle" aria-hidden="true"></i>Customer Group</a>
								</li>
								@endif
								@if(in_array('16', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/customer') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-users" aria-hidden="true"></i>Customer</a>
								</li>
								@endif
								@if(in_array('17', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/couponcode') }}" data-toggle="dropdown" data-i18n="couponcode">
								<i class="fa fa-gift" aria-hidden="true"></i>Coupon Code</a>
								</li>
								@endif
							</ul>
						</li>
						@endif
						@if(in_array('18', $accessrights) || in_array('19', $accessrights) || in_array('20', $accessrights))
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
						<i class="feather icon-package"></i><span data-i18n="Apps">Manage Products</span></a>
							<ul class="dropdown-menu">
								@if(in_array('18', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/category') }}" data-toggle="dropdown" data-i18n="Groups">
								<i class="fa fa-certificate" aria-hidden="true"></i>Category</a>
								</li>
								@endif
								@if(in_array('19', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/brands') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-sun-o" aria-hidden="true"></i>Brands</a>
								</li>
								@endif
								@if(in_array('20', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/products') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-product-hunt" aria-hidden="true"></i>Products</a>
								</li>
								@endif
							</ul>
						</li>
						@endif
						@if(in_array('21', $accessrights) || in_array('22', $accessrights) || in_array('23', $accessrights))
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
						<i class="fa fa-money" aria-hidden="true"></i><span data-i18n="Apps">Sales Order</span></a>
							<ul class="dropdown-menu">
								@if(in_array('21', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/orders') }}" data-toggle="dropdown" data-i18n="Groups">
							   <i class="fa fa-bell-o" aria-hidden="true"></i>Online Order</a>
								</li>
								@endif
								@if(in_array('22', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Customer">
								<i class="feather icon-message-square"></i>Request for Quote</a>
								</li>
								@endif
								@if(in_array('23', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/archives') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-archive" aria-hidden="true"></i>Archives (Deleted Order)</a>
								</li>
								@endif
							</ul>
						</li>
						@endif
						@if(in_array('24', $accessrights))
						<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/subscriber') }}">
						<i class="fa fa-envelope" aria-hidden="true"></i>
						<span data-i18n="Subscriber">Subscriber</span></a>
						</li>
						@endif
						@if(in_array('25', $accessrights) || in_array('26', $accessrights) || in_array('27', $accessrights) || in_array('28', $accessrights) || in_array('29', $accessrights) || in_array('30', $accessrights) || in_array('31', $accessrights) || in_array('32', $accessrights) || in_array('33', $accessrights) || in_array('34', $accessrights))
						<li class="dropdown nav-item" data-menu="dropdown">
						<a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
					   <i class="fa fa-cog" aria-hidden="true"></i><span data-i18n="Settings">Settings</span></a>
							<ul class="dropdown-menu">
								@if(in_array('25', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/subadmin_settings') }}" data-toggle="dropdown" data-i18n="Groups">
								<i class="fa fa-sitemap" aria-hidden="true"></i>Site Configuration</a>
								</li>
								@endif
								@if(in_array('26', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/payment_settings') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-snowflake-o" aria-hidden="true"></i>Payment Configuration</a>
								</li>
								@endif
								@if(in_array('27', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/paymentmethods') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-credit-card-alt" aria-hidden="true"></i>Payment Methods</a>
								</li>
								@endif
								@if(in_array('28', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/shipping_methods') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-houzz" aria-hidden="true"></i>Delivery Types</a>
								</li>
								@endif
								@if(in_array('29', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/country') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-medium" aria-hidden="true"></i>Shipping Countries</a>
								</li>
								@endif
								@if(in_array('30', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/local_shipping_methods') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-asterisk" aria-hidden="true"></i>Local Shipping Methods</a>
								</li>
								@endif
								@if(in_array('31', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/international_shipping_methods') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-plane" aria-hidden="true"></i>International Shipping Methods</a>
								</li>
								@endif
								@if(in_array('32', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/roleandrights') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-sitemap" aria-hidden="true"></i>Role & Rights</a>
								</li>
								@endif
								@if(in_array('33', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/manageadmin') }}" data-toggle="dropdown" data-i18n="Customer">
								<i class="fa fa-address-card" aria-hidden="true"></i>Manage Admin</a>
								</li>
								@endif
								@if(in_array('34', $accessrights))
								<li data-menu="">
								<a class="dropdown-item" href="{{ url('/admin/passwordchange') }}" data-toggle="dropdown" data-i18n="Customer">
							   <i class="fa fa-key" aria-hidden="true"></i>Change Password</a>
								</li>
								@endif
							</ul>
						</li>
						@endif
					@endif		
						<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/logout') }}">
						<i class="feather icon-power"></i>
						<span data-i18n="Logout">{{ __('Logout') }}</span>
						</a>					
						</li>
					</ul>
				</div>
			@endif		
		</div>		
    </div>
    <!-- END: Main Menu-->
@else
	@php
		@header('location:/admin/');
		@exit;
	@endphp	
@endif