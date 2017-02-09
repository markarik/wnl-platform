@extends('layouts.payment')

@section('content')
<div class="row">
	<div class="col-xs-12 text-center">
		<h2>@lang('payment.select-product-title')</h2>
		<p class="lead">@lang('payment.select-product-lead')</p>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<h3>@lang('payment.select-product-onsite-heading')</h3>
		<ul class="list-group">
			<li class="list-group-item">@lang('payment.select-product-features-length')</li>
			<li class="list-group-item">@lang('payment.select-product-features-elearning')</li>
			<li class="list-group-item">@lang('payment.select-product-features-materials')</li>
			<li class="list-group-item">@lang('payment.select-product-features-workshops')</li>
			<li class="list-group-item">@lang('payment.select-product-features-individual')</li>
		</ul>
	</div>
	<div class="col-xs-12 col-sm-6">
		<h3>@lang('payment.select-product-online-heading')</h3>
		<ul class="list-group">
			<li class="list-group-item">@lang('payment.select-product-features-length')</li>
			<li class="list-group-item">@lang('payment.select-product-features-elearning')</li>
			<li class="list-group-item">@lang('payment.select-product-features-materials')</li>
		</ul>
	</div>
</div>
<div class="row text-center">
	<div class="col-xs-12 col-sm-6">
		<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}">@lang('payment.select-product-onsite-button-label')</a>
	</div>
	<div class="col-xs-12 col-sm-6">
		<a href="{{route('payment-personal-data', 'wnl-online')}}">@lang('payment.select-product-online-button-label')</a>
	</div>
</div>
@endsection