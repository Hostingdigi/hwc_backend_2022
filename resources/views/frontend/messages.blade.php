@if(Session::get('success'))
	<div class="alert success" align="center">{{ Session::get('success') }}</div>
@endif

@if(Session::get('failure'))
	<div class="alert danger" align="center">{{ Session::get('failure') }}</div>
@endif

@if(Session::get('message'))
	<div class="alert dancer" align="center">{{ Session::get('message') }}</div>
@endif