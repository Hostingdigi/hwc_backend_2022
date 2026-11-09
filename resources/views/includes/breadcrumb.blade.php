<section id="breadcrumbs" class="breadcrumbs">
  <div class="container">
	<ol>
		<li><a href="{{ url('/') }}">Home</a></li>
		@if(isset($grand_parent_name) && trim($grand_parent_name)!='')
		<li><a href="{{ url('category/'.$grand_parent_url) }}">{{ $grand_parent_name }}</a></li>
		@endif
		@if(isset($parent_name) && trim($parent_name)!='')
		<li><a href="{{ url('type/'.$parent_url) }}">{{ $parent_name }}</a></li>
		@endif
		<li><div class="pageheading">{{$page_title}}</div></li>
	</ol>
  </div>
</section>