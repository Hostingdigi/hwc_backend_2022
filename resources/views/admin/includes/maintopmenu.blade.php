<div id="wrapper">
<div id="header">
    <div id="head_lt">
    <!--Logo Start from Here--><span class="slogan"> &nbsp;&nbsp;HardwareCity administration suite</span>
    <!--Logo end  Here-->
    </div>
	
</div>
<div class="menubg">
        <nav>
                    <ul  class="nav">
                <li><a href="#">Content &amp; Masthead</a>                
						<ul>
                        <li>
							<a href="{{ url('/admin/static_pages') }}">Page Content</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/banner_master') }}">Masthead Images</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/promotions') }}">Home Banners</a>
						</li> 
                         <li>
							<a href="{{ url('/admin/banner_ads') }}">Advertisement Banners</a>
						</li>
						</ul>					
                </li>
                 <li class="" "><a href="#">Manage Customer</a>
                		<ul>
                        <li>
							<a href="{{ url('/admin/customergroup') }}">Customer Group</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/customer') }}">Customer</a>
						</li> 
						</ul>					
                </li>
                 <li class="" ">
                 <a href="#">Manage Products</a>                
						<ul>
						<li>
							<a href="{{ url('/admin/producttag') }}">Product Tag</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/category') }}">Category</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/brands') }}">Brands</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/products') }}">Products</a>
						</li> 
						</ul>					
                </li>
                <li class=""><a href="#">Sales Order</a>                
						<ul>
                        <li>
							<a href="{{ url('/admin/orders') }}">Online Order</a>
						</li> 
                        <li>
							<a href="{{ url('/admin/request_for_quote') }}">Request for Quote</a>
						</li> 
						<li>
							<a href="{{ url('/admin/archives') }}">Archives (Deleted Order)</a>
						</li>                         
						</ul>					
                </li>               
                <li class=""><a href="{{ url('/admin/subscriber') }}">Subscriber</a></li>        
				<li class=""><a href="#">Settings</a>					
						<ul>
                        	<li>
								<a href="{{ url('/admin/subadmin_settings') }}">Site Configuration</a>
							</li> 
                            <li>
								<a href="{{ url('/admin/payment_settings') }}">Payment Configuration</a>
							</li>  
							<li>
								<a href="{{ url('/admin/payment_method') }}">Payment Methods</a>
							</li> 
                            <li>
								<a href="{{ url('/admin/shipping_methods') }}">Delivery Types</a>
							</li> 
         
                            <li>
								<a href="{{ url('/admin/country') }}">Shipping Countries</a>
							</li> 
                             <li>
								<a href="{{ url('/admin/local_shipping_methods') }}">Local Shipping Methods</a>
							</li> 
                             <li>
								<a href="{{ url('/admin/international_shipping_methods') }}">International Shipping Methods</a>
							</li> 
                            
                             <li>
								<a href="{{ url('/admin/manageadmin') }}">Manage Admin</a>
							</li>
							 <li>
								<a href="{{ url('/admin/changepassword') }}">Change Password</a>
							</li> 
						</ul>					
				</li>		
			</ul>       
        <div class="logout"><a href="{{ url('/admin/logout') }}">
        <img src="{{ asset('images/logout.gif') }}"></a></div>
        </nav>
</div>