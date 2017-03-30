@extends('payment.layout')

@section('content')

@include('payment.payment-hero', [
	'step' => 1,
	'title' => trans('payment.select-product-title'),
	'subtitle' => trans('payment.select-product-subtitle'),
])

<section class="section">
	<div class="container">
		<div class="columns is-hidden-mobile has-text-centered">
			<div class="column">
				<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}"
				   class="button is-primary {{ !$onsite->available ? 'is-disabled' : '' }}"
				>
					@lang('payment.select-product-onsite-button-label')
				</a>
			</div>
			<div class="column">
				<a href="{{route('payment-personal-data', 'wnl-online')}}"
				   class="button is-primary is-outlined {{ !$online->available ? 'is-disabled' : '' }}"
				>
					@lang('payment.select-product-online-button-label')
				</a>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="box">
					@if(!$onsite->available)
						<h3>Brak miejsc</h3>
					@endif
					<p class="title">@lang('payment.select-product-onsite-heading')</p>
					<p class="subtitle">@lang('common.currency', ['value' => 2200])</p>
					<p class="caption">@lang('payment.select-product-coupon', ['value' => 2000])</p>
					<ul class="list-group">
						@lang('payment.select-product-online-description')
					</ul>
					<span class="onsite-plus">+</span>
					<ul class="list-group">
						@lang('payment.select-product-onsite-description')
					</ul>
				</div>
				<div class="block has-text-centered is-hidden-tablet">
					<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary">
						@lang('payment.select-product-onsite-button-label')
					</a>
				</div>
			</div>
			<div class="column">
				<div class="box">
					@if(!$online->available)
						<h3>Brak miejsc</h3>
					@endif
					<p class="title">@lang('payment.select-product-online-heading')</p>
					<p class="subtitle">@lang('common.currency', ['value' => '1500'])</p>
					<p class="caption">@lang('payment.select-product-coupon', ['value' => 1300])</p>
					<ul class="list-group">
						@lang('payment.select-product-online-description')
					</ul>
				</div>
				<div class="block has-text-centered is-hidden-tablet">
					<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary is-outlined">
						@lang('payment.select-product-online-button-label')
					</a>
				</div>
			</div>
		</div>
		<div class="columns is-hidden-mobile has-text-centered">
			<div class="column">
				<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}"
				   class="button is-primary {{ !$onsite->available ? 'is-disabled' : '' }}"
				>
					@lang('payment.select-product-onsite-button-label')
				</a>
			</div>
			<div class="column">
				<a href="{{route('payment-personal-data', 'wnl-online')}}"
				   class="button is-primary is-outlined {{ !$online->available ? 'is-disabled' : '' }}"
				>
					@lang('payment.select-product-online-button-label')
				</a>
			</div>
		</div>
	</div>
</section>
<section class="section">
	<div class="container">
		<div class="columns">
			<div class="column">
				<div class="box">
					<h3 class="title">@lang('payment.select-product-workshops-heading')</h3>
					<p>@lang('payment.select-product-workshops-description')</p>
					<p class="margin vertical has-text-centered">
						<a href="https://wiecejnizlek.pl/o-warsztatach">@lang('payment.select-product-read-more')</a>
					</p>
				</div>
			</div>
			<div class="column">
				<div class="box">
					<h3 class="title">@lang('payment.select-product-q-and-a-heading')</h3>
					<p>@lang('payment.select-product-q-and-a-description')</p>
					<p class="margin vertical has-text-centered">
						<a href="https://wiecejnizlek.pl/o-kursie">@lang('payment.select-product-read-more')</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
