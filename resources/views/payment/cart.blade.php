
<div class="cart o-cart{{!empty($coupon) ? ' has-coupon' : ''}}">
	@if(!empty($productName))
		<header class="o-cart__headline -centeredSpread">
			<span>@lang('payment.cart-header')</span>
			<span class="icon -stormGrey">
				<i class="fa fa-times a-icon -small"></i>
			</span>
		</header>
		<section class="o-cart__card -shadowMedium">
			<img src="{{ asset('/images/lek-product-logo.svg') }}" class="o-cart__card__logo">
			<p class="o-cart__card__text">
				<span>{{$productName}}</span>
				@if (!empty($productAccessEnd))
					<span class="-textMinus2">
						@lang('payment.cart-access-info', ['date' => $productAccessEnd->format('d.m.Y')])
					</span>
				@endif
			</p>
		</section>
		<section class="o-cart__details -stormGrey -textMinus1">
			<p class="o-cart__details__item -centeredSpread">
				<span>@lang('payment.cart-shipment-label')</span>
				<span>@lang('payment.cart-shipment-value')</span>
			</p>
			@if(!empty($coupon))
				<p class="o-cart__details__item -centeredSpread">
					<span>@lang('payment.cart-coupon-label')</span>
					<span>
						-{{$coupon->is_percentage
						? trans('payment.voucher-percentage', ['value' => $coupon->value])
						: trans('payment.voucher-amount', ['value' => $coupon->value])}}
					</span>
				</p>
			@endif
		</section>
		<section>
			@if(!empty($coupon))
				<p class="-centeredSpread">
					<span>@lang('payment.cart-price-label')</span>
					<span class="strikethrough">
						@lang('payment.cart-price-value', ['value' => $productPrice])
					</span>
					<span>@lang('payment.cart-price-value', ['value' => $productPriceWithCoupon])</span>
				</p>
			@else
				<p class="-centeredSpread">
					<span>@lang('payment.cart-price-label')</span>
					<span>@lang('payment.cart-price-value', ['value' => $productPrice])</span>
				</p>
			@endif
		</section>
	@else
		<span>@lang('payment.cart-empty')</span>
	@endif
</div>
