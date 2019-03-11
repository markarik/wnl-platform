
<div class="section has-text-left cart{{$hasCoupon ? ' has-coupon' : ''}}">
    @if(!empty($productName))
        <h3>Twoje zamówienie</h3>
        <div>Dostęp od momentu wpłaty do {{$productAccessEnd->format('d.m.Y')}}</div>
        <div class="card">
            <p><span>Wysyłka:</span><span>Na terenie Polski za darmo</span></p>
            @if(!empty($hasCoupon))
                <p><span>Zniżka:</span><span>{{$couponValue}}</span></p>
            @endif
        </div>
        <div class="card">
            <span>Kwota całkowita:</span><span>{{$productPrice}}zł</span>
            @if (!empty($hasCoupon))
                <span>{{$productPriceWithCoupon}}zł</span>
            @endif
        </div>
@else
    <span>Twój koszyk jest pusty</span>
@endif
</div>
