@extends('layouts.guest', ['logoutRedirectToRoute' => 'payment-account'])

@section('scripts')
	<script src="{{ mix('js/payment.js') }}"></script>
	@yield('payment-scripts')
@endsection
