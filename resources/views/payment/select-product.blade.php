@extends('payment.layout')

@section('content')

@include('payment.payment-hero', [
	'step' => 1,
	'title' => trans('payment.select-product-title'),
	'subtitle' => trans('payment.select-product-subtitle'),
])

<section class="section select-product">
	<div class="container">
		@if(!Auth::user())
			<div class="box aligncenter">
				<h4>@lang('payment.select-product-account-heading')</h4>
				<p>@lang('payment.select-product-account-lead')</p>
			</div>
		@endif
		@if($online->signups_close->isPast() && $onsite->signups_close->isPast())
			<div class="column">
				<div class="notification has-text-centered strong">
					Zapisy zostały zakończone. <a href="https://wiecejnizlek.pl/zostaw-e-mail">Kliknij i zostaw swój e-mail</a>, aby zarezerwować miejsce na kolejnej edycji!
				</div>
			</div>
		@elseif($online->signups_start->isPast() && $onsite->signups_start->isPast())
			@if ($participantCoupon)
				<div class="box wnl-album">
					<div class="columns">
						<div class="column">
							<a href="https://wiecejnizlek.pl/album" target="_blank">
								<img src="{{ asset('/images/album.jpg') }}" alt="Mapa myśli - Słoń">
							</a>
						</div>
						<div class="column">
							<h2 class="title">Nowy album map myśli</h2>
							<p class="subtitle">oraz system skojarzeniowo-wyobrażeniowy!</p>
							<div class="content">
								<p class="big">
									Projektując nowy album map myśli uwzględniliśmy wszystkie Wasze uwagi - mapy są lepiej rozplanowane, niemal wszystkie jednostki chorobowe posiadają przypisane postacie, a nagrania audio zawierają dużo więcej informacji medycznych! <span class="small">(No i lektor ma perfekcyjną&nbsp;dykcję) 😉</span>
								</p>
								<p>
									Korzystanie ze starego albumu wciąż jest możliwe, polecamy jednak zanurzenie się w zupełnie nowy świat skojarzeń z uaktualnioną wersją! Używając zniżki 50% na Kurs internetowy nie otrzymujesz nowych materiałów, ale możesz zamówić je już teraz dodatkowo!
								</p>
								<p class="big">
									Nowy album, praktyczna torba, nowe pisaki, plan pracy i wysyłka w cenie 300zł!
								</p>
								<p class="aligncenter margin vertical">
									<a href="{{route('payment-personal-data', 'wnl-album')}}" class="button is-primary">
										Zamów nowy album
									</a>
								</p>
							</div>
						</div>
					</div>
				</div>
			@endif
			<div class="columns is-hidden-mobile has-text-centered">
				<div class="column">
					@if(!$online->available)
						<div class="notification has-text-centered strong">Brak miejsc :(</div>
					@else
						<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary"
						   id="btest-wnl-online-button">
							@lang('payment.select-product-online-button-label')
						</a>
						<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
					@endif
				</div>
				 <div class="column">
					@if(!$onsite->available)
						<div class="notification has-text-centered strong">Brak miejsc :(</div>
					@else
						<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary is-outlined"
							id="btest-wnl-online-onsite-button">
							@lang('payment.select-product-onsite-button-label')
						</a>
						<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
					@endif
				</div>
			</div>
		@endif
		@if($online->signups_start->isFuture() && $onsite->signups_start->isFuture())
			<div class="notification has-text-centered strong">
				Do otwarcia zapisów pozostało:
				<div class="signups-countdown" data-start="{{ $online->signups_start->timestamp }}">
					sprawdzam zegarek...
				</div>
			</div>
			<div class="notification has-text-centered strong">
				<a href="https://wiecejnizlek.pl/zostaw-e-mail">Kliknij i zostaw swój e-mail</a>, aby zarezerwować miejsce na kolejnej edycji!
			</div>
		@endif
		<div class="columns">
			<div class="column">
				@if($online->signups_start->isPast() && $online->signups_close->isFuture())
					<div class="block has-text-centered is-hidden-tablet">
						@if(!$online->available)
							<div class="notification has-text-centered strong">Brak miejsc :(</div>
						@else
							<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary">
								@lang('payment.select-product-online-button-label')
							</a>
							<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
						@endif
					</div>
				@endif
				<div class="box">
					<p class="title">@lang('payment.select-product-online-heading')</p>
					<p class="subtitle">
						@if (Auth::user() && Auth::user()->coupons->count() !== 0)
							<span class="strikethrough">@lang('common.currency', ['value' => 1500])</span>
							@lang('common.currency', ['value' => 750])
						@else
							@lang('common.currency', ['value' => 1500])
						@endif
					</p>
					<ul class="list-group">
						@lang('payment.select-product-online-description')
					</ul>
				</div>
				@if($online->signups_start->isPast() && $online->signups_close->isFuture())
					<div class="block has-text-centered is-hidden-tablet">
						@if(!$online->available)
							<div class="notification has-text-centered strong">Brak miejsc :(</div>
						@else
							<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary">
								@lang('payment.select-product-online-button-label')
							</a>
							<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
						@endif
					</div>
				@endif
			</div>
			<div class="column">
				@if($onsite->signups_start->isPast() && $onsite->signups_close->isFuture())
					<div class="block has-text-centered is-hidden-tablet">
						@if(!$onsite->available)
							<div class="notification has-text-centered strong">Brak miejsc :(</div>
						@else
							<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary is-outlined">
								@lang('payment.select-product-onsite-button-label')
							</a>
							<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
						@endif
					</div>
				@endif
				<div class="box">
					<p class="title">@lang('payment.select-product-onsite-heading')</p>
					<p class="subtitle">@lang('common.currency', ['value' => 2000])</p>
					<ul class="list-group">
						@lang('payment.select-product-online-description')
					</ul>
					<span class="onsite-plus">+</span>
					<ul class="list-group">
						@lang('payment.select-product-onsite-description')
					</ul>
				</div>
				@if($onsite->signups_start->isPast() && $onsite->signups_close->isFuture())
					<div class="block has-text-centered is-hidden-tablet">
						@if(!$onsite->available)
							<div class="notification has-text-centered strong">Brak miejsc :(</div>
						@else
							<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary is-outlined">
								@lang('payment.select-product-onsite-button-label')
							</a>
							<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
						@endif
					</div>
				@endif
			</div>
		</div>
		@if($online->signups_close->isPast() && $onsite->signups_close->isPast())
			<div class="column">
				<div class="notification has-text-centered strong">
					Zapisy zostały zakończone. <a href="https://wiecejnizlek.pl/zostaw-e-mail">Kliknij i zostaw swój e-mail</a>, a przypomnimy Ci o kolejnych!
				</div>
			</div>
		@elseif($online->signups_start->isPast() && $onsite->signups_start->isPast())
			<div class="columns is-hidden-mobile has-text-centered">
				<div class="column">
					@if(!$online->available)
						<div class="notification has-text-centered strong">Brak miejsc :(</div>
					@else
						<a href="{{route('payment-personal-data', 'wnl-online')}}" class="button is-primary">
							@lang('payment.select-product-online-button-label')
						</a>
						<p class="metadata has-text-centered">Pozostało miejsc: {{ $online->quantity }}/{{ $online->initial }}</p>
					@endif
				</div>
				 <div class="column">
					@if(!$onsite->available)
						<div class="notification has-text-centered strong">Brak miejsc :(</div>
					@else
						<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}" class="button is-primary is-outlined">
							@lang('payment.select-product-onsite-button-label')
						</a>
						<p class="metadata has-text-centered">Pozostało miejsc: {{ $onsite->quantity }}/{{ $onsite->initial }}</p>
					@endif
				</div>
			</div>
		@endif
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

@section('payment-scripts')
	<script>typeof fbq === 'function' && fbq('track', 'AddToCart')</script>
@endsection
