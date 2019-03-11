
<div class="card column cart{{$hasCoupon ? ' has-coupon' : ''}}">
    @if(!empty($productName))
    <header class="card-header">
        <h3 class="card-header-title">@lang('payment.cart-header')</h3>
    </header>
    <div class="card-content">
        <p>
            @lang('payment.cart-access-info', ['date' => $productAccessEnd->format('d.m.Y')])
        </p>
        <p>
            <span>@lang('payment.cart-shipment-label')</span>
            <span>@lang('payment.cart-shipment-value')</span>
        </p>
        @if(!empty($hasCoupon))
            <p>
                <span>@lang('payment.cart-coupon-label')</span><span>{{$couponValue}}</span>
            </p>
        @endif
        @if (empty($hasCoupon))
            <span>@lang('payment.cart-price-label')</span><span>@lang('payment.cart-price-value', ['value' => $productPrice])</span>
        @else
            <span>@lang('payment.cart-price-label')</span>
            <span class="strikethrough">
                @lang('payment.cart-price-value', ['value' => $productPrice])
            </span>
            <span>@lang('payment.cart-price-value', ['value' => $productPriceWithCoupon])</span>
        @endif
    </div>
    @else
        <span>@lang('payment.cart-empty')</span>
    @endif
</div>
