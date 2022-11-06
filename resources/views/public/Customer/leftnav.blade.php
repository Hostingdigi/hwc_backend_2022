<nav class="mb-10 mb-6 mb-md-0">
  <div class="list-group list-group-sm list-group-strong list-group-flush-x">
	<a class="list-group-item list-group-item-action dropright-toggle @if($id == 'myorders') active @endif" href="{{ url('/customer/myorders') }}">
	  Orders
	</a>
	<a class="list-group-item list-group-item-action dropright-toggle @if($id == 'pendingorders') active @endif" href="{{ url('/customer/pendingorders') }}">
	  Pending Orders
	</a>	
	<a class="list-group-item list-group-item-action dropright-toggle @if($id == 'quotations') active @endif" href="{{ url('/customer/quotations') }}">
	  Quotations
	</a> 	
	<a class="list-group-item list-group-item-action dropright-toggle @if($id == 'personalinfo') active @endif" href="{{ url('/customer/personalinfo') }}">
	  Personal Info
	</a>
	<a class="list-group-item list-group-item-action dropright-toggle @if($id == 'address') active @endif" href="{{ url('/customer/address') }}">
	  Addresses
	</a>
	<a class="list-group-item list-group-item-action dropright-toggle @if($id == 'changepassword') active @endif" href="{{ url('/customer/changepassword') }}">
	  Change Password
	</a>
	<a class="list-group-item list-group-item-action dropright-toggle" href="{{ url('/logout') }}">
	  Logout
	</a>
  </div>
</nav>