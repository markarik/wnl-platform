@extends('layouts.guest')

@section('content')
	<div class="container tou-container">
		<section class="subsection">
			<h1>Cześć {{ $user->first_name }},</h1>

			<p>

			</p>
			Nowy regulamin znajdziesz pod
			<a href="@lang('payment.tou-link-href')" target="_new"> tutaj</a>
		</section>

		<section class="subsection">
			<form action="{{ route('terms-accept') }}" method="post">
				{{ csrf_field() }}
				<div class="has-text-centered">
					<button class="button is-primary">
						Akceptuję nowy regulamin
					</button>
				</div>
			</form>
		</section>

		<section class="subsection">
			<p>
				Akceptacja regulaminu jest konieczna, aby korzystać z platformy.
				Jeśli masz wątpliwości dotycząte nowego regulaminu lub
				chcesz zrezygnować z dostępu do platformy, napisz do nas na
				<a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>
			</p>
		</section>
	</div>
@endsection