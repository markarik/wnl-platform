@extends('payment.layout')

@section('content')

@include('payment.payment-hero', [
	'step' => 1,
	'title' => trans('payment.select-product-title'),
	'subtitle' => trans('payment.select-product-subtitle'),
])

<section class="section">
	<div class="container">
		 <div class="has-text-centered">
			<p class="margin bottom">
				<a href="{{route('payment-personal-data', 'wnl-album')}}">
					Zamów tylko nowe materiały
				</a>
			</p>
		</div> 
		<div class="columns is-hidden-mobile has-text-centered">
			 <div class="column">
				@if(!$onsite->available)
					<div class="notification has-text-centered strong">Brak miejsc :(</div>
				@else
					<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary">
						@lang('payment.select-product-onsite-button-label')
					</a>
					<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
				@endif
			</div>
			<div class="column">
				@if(!$online->available)
					<div class="notification has-text-centered strong">Brak miejsc :(</div>
				@else
					<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary is-outlined">
						@lang('payment.select-product-online-button-label')
					</a>
					<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
				@endif
			</div>
			{{--<div class="column">
				<div class="notification has-text-centered strong">
					Zapisy ruszają 3 kwietnia o godz. 12:00! <a href="https://wiecejnizlek.pl/zostaw-e-mail">Kliknij i zostaw swój e-mail</a>, a przypomnimy Ci o nich!
				</div>
			</div>--}}
		</div>
		<div class="columns">
			<div class="column">
				<div class="box">
					<p class="title">@lang('payment.select-product-onsite-heading')</p>
					<p class="subtitle">@lang('common.currency', ['value' => 2000])</p>
					{{-- <p class="caption">@lang('payment.select-product-coupon', ['value' => 2000])</p> --}}
					<ul class="list-group">
						@lang('payment.select-product-online-description')
					</ul>
					<span class="onsite-plus">+</span>
					<ul class="list-group">
						@lang('payment.select-product-onsite-description')
					</ul>
				</div>
				<div class="block has-text-centered is-hidden-tablet">
					@if(!$onsite->available)
						<div class="notification has-text-centered strong">Brak miejsc :(</div>
					@else
						<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary">
							@lang('payment.select-product-onsite-button-label')
						</a>
						<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
					@endif
				</div>
			</div>
			<div class="column">
				<div class="box">
					<p class="title">@lang('payment.select-product-online-heading')</p>
					<p class="subtitle">@lang('common.currency', ['value' => 1500])</p>
					{{-- <p class="caption">@lang('payment.select-product-coupon', ['value' => 1300])</p> --}}
					<ul class="list-group">
						@lang('payment.select-product-online-description')
					</ul>
				</div>
				<div class="block has-text-centered is-hidden-tablet">
					@if(!$online->available)
						<div class="notification has-text-centered strong">Brak miejsc :(</div>
					@else
					<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary is-outlined">
						@lang('payment.select-product-online-button-label')
					</a>
					<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
					@endif
				</div>
			</div>
		</div>
		<div class="columns is-hidden-mobile has-text-centered">
			 <div class="column">
				@if(!$onsite->available)
					<div class="notification has-text-centered strong">Brak miejsc :(</div>
				@else
					<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary">
						@lang('payment.select-product-onsite-button-label')
					</a>
					<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
				@endif
			</div>
			<div class="column">
				@if(!$online->available)
					<div class="notification has-text-centered strong">Brak miejsc :(</div>
				@else
				<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary is-outlined">
					@lang('payment.select-product-online-button-label')
				</a>
				<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
				@endif
			</div>
			{{--<div class="column">
				<div class="notification has-text-centered strong">
					Zapisy ruszają 3 kwietnia o godz. 12:00! <a href="https://wiecejnizlek.pl/zostaw-e-mail">Kliknij i zostaw swój e-mail</a>, a przypomnimy Ci o nich!
				</div>
			</div>--}}
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
						<a href="https://wiecejnizlek.pl/o-warsztatach" target="_blank">@lang('payment.select-product-read-more')</a>
					</p>
				</div>
			</div>
			<div class="column">
				<div class="box">
					<h3 class="title">@lang('payment.select-product-q-and-a-heading')</h3>
					<p>@lang('payment.select-product-q-and-a-description')</p>
					<p class="margin vertical has-text-centered">
						<a href="https://wiecejnizlek.pl/o-kursie" target="_blank">@lang('payment.select-product-read-more')</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
