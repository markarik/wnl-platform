@extends('layouts.checkout')

@section('scripts')
	<script src="{{ mix('js/payment.js') }}"></script>
	@yield('payment-scripts')
@endsection
