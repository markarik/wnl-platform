@if(!empty($productName))
    <div class="section has-text-left">
        <h3>Twoje zamówienie</h3>
        <div>Dostęp od momentu wpłaty do {{$productAccessEnd->format('d.m.Y')}}</div>
        <div class="card">
            <p><span>Wysyłka:</span><span>Na terenie Polski za darmo</span></p>
            <p><span>Zniżka:</span><span>{{$couponValue}}</span></p>
        </div>
        <div class="card">
            <span>Kwota całkowita:</span><span>{{$productPrice}}zł</span>&nbsp;<span>{{$productPriceWithCoupon}}zł</span>
        </div>
    </div>
@endif
