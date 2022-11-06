@extends('layouts.app')

@section('content')
<div class="container">
	<div class="col-12 section-title">
		<h2>{{ $page->EnTitle }}</h2>
	</div>
    <div class="row justify-content-center">		
        <div class="col-md-12">
		{!! $page->ChContent !!}
        </div>
    </div>
</div>
@endsection
