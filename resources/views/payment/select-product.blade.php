@extends('layouts.payment')

@section('content')
<section class="hero is-primary is-bold">
	<div class="hero-body has-text-centered">
		<div class="container">
			<h1 class="title">
				@lang('payment.select-product-title')
			</h1>
			<h2 class="subtitle">
				@lang('payment.select-product-lead')
			</h2>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="columns">
			<div class="column">
				<div class="box">
					<p class="title">@lang('payment.select-product-onsite-heading')</p>
					<ul class="list-group">
						<li class="list-group-item">@lang('payment.select-product-features-length')</li>
						<li class="list-group-item">@lang('payment.select-product-features-elearning')</li>
						<li class="list-group-item">@lang('payment.select-product-features-materials')</li>
						<li class="list-group-item">@lang('payment.select-product-features-workshops')</li>
						<li class="list-group-item">@lang('payment.select-product-features-individual')</li>
					</ul>
				</div>
				<div class="block has-text-centered is-hidden-tablet">
					<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}">
						@lang('payment.select-product-onsite-button-label')
					</a>
				</div>
			</div>
			<div class="column">
				<div class="box">
					<p class="title">@lang('payment.select-product-online-heading')</p>
					<ul class="list-group">
						<li class="list-group-item">@lang('payment.select-product-features-length')</li>
						<li class="list-group-item">@lang('payment.select-product-features-elearning')</li>
						<li class="list-group-item">@lang('payment.select-product-features-materials')</li>
					</ul>
				</div>
				<div class="block has-text-centered is-hidden-tablet">
					<a href="{{route('payment-personal-data', 'wnl-online')}}">
						@lang('payment.select-product-online-button-label')
					</a>
				</div>
			</div>
		</div>
		<div class="columns is-hidden-mobile has-text-centered">
			<div class="column">
				<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary">
					@lang('payment.select-product-onsite-button-label')
				</a>
			</div>
			<div class="column">
				<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary is-outlined">
					@lang('payment.select-product-online-button-label')
				</a>
			</div>
		</div>
	</div>
</section>

@endsection