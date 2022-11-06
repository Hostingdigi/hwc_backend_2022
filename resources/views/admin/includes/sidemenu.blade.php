    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="#">
                        <!--div class="brand-logo"></div-->
                        <h2 class="brand-text mb-0">Hardwarecity</h2>
                    </a></li>
                <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                
                <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class=" nav-item"><a href="{{ url('/admin/dashboard') }}">
                    <i class="feather icon-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">Dashboard</span></a>
                </li>                                             
                <li class=" nav-item"><a href="#"><i class="feather icon-user"></i>
                <span class="menu-title" data-i18n="User">Customer</span></a>
                    <ul class="menu-content">
                        <li><a href="{{ url('/admin/customer_group') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n="List">Customer Group</span></a>
                        </li>
                        <li><a href="{{ url('/admin/customer') }}"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Valuable Users">Customer</span></a>
                        </li>                      
                    </ul>
                </li>
                <li class=" nav-item"><a href="#"><i class="feather icon-layout"></i>
                <span class="menu-title" data-i18n="Page Configuration">Content and Masthead</span></a>
                    <ul class="menu-content">
                        <li><a href="{{ url('/admin/page_content') }}"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Home Page Configuration">Page Content</span></a>
                        </li>
                        <li><a href="{{ url('/admin/masthead_image') }}"><i class="feather icon-circle">
                            </i><span class="menu-item" data-i18n="Slider">Masthead Images</span></a>
                        </li>
                        <li><a href="{{ url('/admin/home_banner') }}"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Banner">Home Banner</span></a>
                        </li>
                        <li><a href="{{ url('/admin/advertising_banner') }}"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Menus">Advertisement Banner</span></a>
                        </li>                        
                    </ul>
                </li>               
                <li class=" nav-item"><a href="#"><i class="feather icon-shopping-cart"></i>
                    <span class="menu-title" data-i18n="Sales Order">Online Order</span></a>
                    <ul class="menu-content">
                        <li><a href="#"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n="Online Order">Online Order</span></a>
                        </li>
                        <li><a href="#"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">Request for Quote">Request for Quote</span></a>
                        </li>
                        <li><a href="#"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n="Tax Rules">Archieves (Deleted Order)</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" nav-item"><a href="#"><i class="feather icon-list"></i>
                    <span class="menu-title" data-i18n="Manage Products">Manage Products</span>
                        </a>
                    <ul class="menu-content">
                        <li><a href="{{ url('/admin/categories') }}"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Categories">Categories</span></a>
                        </li>
                        <li><a href="{{ url('/admin/product') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n="Products">Products</span></a>
                        </li>
                        <li><a href="{{ url('/admin/brand') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n="Brands">Brands</span></a>
                        </li>
                    </ul>
                </li>                              
                <li class=" nav-item"><a href="#"><i class="feather icon-eye"></i>
                    <span class="menu-title" data-i18n="Order & Payments">Order & Payments</span></a>
                    <ul class="menu-content">
                        <li><a href="#"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Order Report">Order Report</span></a>
                        </li>
                        <li><a href="#"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">Product Report">Product Report</span></a>
                        </li>
                    </ul>
                </li>
                
                <li class=" nav-item"><a href="#"><i class="feather icon-mail"></i>
                    <span class="menu-title" data-i18n="Settings">Settings</span></a>
                    <ul class="menu-content">
                        <!--li><a href="#"><i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Site Configuration">Site Configuration</span></a>
                        </li-->
                        <li><a href="{{ url('/admin/payment_configuration') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">Payment Configuration">Payment Configuration</span></a>
                        </li>
                        <li><a href="{{ url('/admin/delivery_types') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">Delivery Types">Delivery Types</span></a>
                        </li>
                        <li><a href="{{ url('/admin/country') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">Shipping Countries">Shipping Countries</span></a>
                        </li>
                        <li><a href="{{ url('/admin/local_shipping_method') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">Local Shipping Method">Local Shipping Method</span></a>
                        </li>
                        <li><a href="{{ url('/admin/international_shipping_method') }}"><i class="feather icon-circle"></i>
                        <span class="menu-item" data-i18n=">International Shipping Method">International Shipping Method</span></a>
                        </li>
                    </ul>
                </li>     
                <li class=" nav-item"><a href="{{ url('/admin/subscriber') }}">
                    <i class="feather icon-mail"></i>
                    <span class="menu-title" data-i18n="Message">Subsriber</span></a>
                </li>                                                                           
            </ul>
</div>
</div>