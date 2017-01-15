@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Your orders</div>

                    <div class="panel-body">
                        @foreach($orders as $order)

                            @if($order->paid)
                                Zapłacono! Hurra!
                            @endif

                            @if(!$order->paid && $order->method == 'transfer')
                                Przelej hajs na konto: 1234 000 9876 1244 8989 1200 0012
                            @endif

                            @if(!$order->paid && $order->method == 'online')
                                Oczekiwanie na potwierdzenie płatności online
                            @endif
                        @endforeach

                        <a href="{{url('payment/step3')}}" class="button" >Zmień metodę płatności</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
