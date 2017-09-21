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
				<form action="{{ route('payment-voucher-post') }}" method="post">
					{!! csrf_field() !!}
					<label for="code">Kod: </label>
					<input type="text" id="code" name="code" value="{{ request('code') ?? '' }}">
					@foreach ($errors->get('code') as $message)
						<div class="error">{{ $message }}</div>
					@endforeach
				</form>
			</div>
		</div>
	</section>

@endsection
