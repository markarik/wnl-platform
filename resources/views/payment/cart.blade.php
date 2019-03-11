
<div class="card column cart{{$hasCoupon ? ' has-coupon' : ''}}">
    @if(!empty($productName))
    <header class="card-header">
        <h3 class="card-header-title">Twoje zamówienie</h3>
    </header>
    <div class="card-content">
        <div>Dostęp od momentu wpłaty do {{$productAccessEnd->format('d.m.Y')}}</div>
        <p><span>Wysyłka:</span><span>Na terenie Polski za darmo</span></p>
        @if(!empty($hasCoupon))
            <p><span>Zniżka:</span><span>{{$couponValue}}</span></p>
        @endif
        @if (empty($hasCoupon))
            <span>Kwota całkowita:</span><span>{{$productPrice}}zł</span>
        @else
            <span>Kwota całkowita:</span><span class="strikethrough">{{$productPrice}}zł</span>
            <span>{{$productPriceWithCoupon}}zł</span>
        @endif
    </div>
    @else
        <span>Twój koszyk jest pusty</span>
    @endif
</div>
