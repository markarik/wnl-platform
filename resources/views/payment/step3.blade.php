@extends('layouts.payment')

@section('content')

    @lang('payment.your-personal-info')<br>
    {{$order['client']}} <br>
    {{$order['address']}} <br>
    {{$order['zip']}} {{$order['city']}} <br><br>
    {{$order['email']}} <br><br>
    <a href="{{ url('/payment/step2') }}" class="button">@lang('payment.edit-account')</a>

    <button>@lang('payment.bank-transfer-button')</button>

    <form action="{{ config('przelewy24.transaction_url') }}" method="post" class="form">

        <input type="hidden" name="p24_session_id" value="{{$order['session_id']}}"/>
        <input type="hidden" name="p24_merchant_id" value="{{$order['merchant_id']}}"/>
        <input type="hidden" name="p24_pos_id" value="{{$order['merchant_id']}}"/>
        <input type="hidden" name="p24_amount" value="{{$order['amount']}}"/>
        <input type="hidden" name="p24_currency" value="{{$order['currency']}}"/>
        <input type="hidden" name="p24_description" value="{{$order['description']}}"/>
        <input type="hidden" name="p24_client" value="{{$order['client']}}"/>
        <input type="hidden" name="p24_address" value="{{$order['address']}}"/>
        <input type="hidden" name="p24_zip" value="{{$order['zip']}}"/>
        <input type="hidden" name="p24_city" value="{{$order['city']}}"/>
        <input type="hidden" name="p24_country" value="{{$order['country']}}"/>
        <input type="hidden" name="p24_email" value="{{$order['email']}}"/>
        <input type="hidden" name="p24_language" value="{{$order['language']}}"/>
        <input type="hidden" name="p24_url_return" value="{{$order['url_return']}}"/>
        <input type="hidden" name="p24_url_status" value="{{$order['url_status']}}"/>
        <input type="hidden" name="p24_api_version" value="{{config('przelewy24.api_version')}}"/>
        <input type="hidden" name="p24_sign" value="{{$order['sign']}}"/>

        <input name="submit_send" value="@lang('payment.online-payment-button')" type="submit"/>
    </form>

@endsection