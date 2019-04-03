
<div class="t-checkout__cart cart o-cart{{!empty($coupon) ? ' has-coupon' : ''}} -dNone" id="cartBox">
	<div class="o-cart__content">
		@if(!empty($productName))
			<header class="o-cart__headline -centeredSpread">
				<span>@lang('payment.cart-header')</span>
				<i class="a-icon -stormGray -hiddenMAndUp -touchable fa-times -small"  id="cartClose"></i>
			</header>
			<section class="o-cart__card -shadowMedium">
				<img src="{{ asset(config('course.product_logo')) }}" class="o-cart__card__logo">
				<p class="o-cart__card__text">
					<span>{{$productName}}</span>
					@if (!empty($productAccessEnd))
						<span class="-textMinus2">
							@lang('payment.cart-access-info', [
								'date' => '<span data-date-format="D MMMM Y" data-timestamp="' . $productAccessEnd->timestamp . '">' . $productAccessEnd->format('d.m.Y') . '</span>'
							])
						</span>
					@endif
				</p>
			</section>
			<section class="o-cart__details">
				<p class="m-checkoutListItem">
					@if(empty($coupon) || $coupon->kind !== App\Models\Coupon::KIND_PARTICIPANT)
						<span>@lang('payment.cart-shipment-label')</span>
						<span class="m-checkoutListItem__value">@lang('payment.cart-shipment-value')</span>
					@else
						<span>@lang('payment.cart-no-album-info')</span>
					@endif
				</p>
				@if(!empty($coupon))
					<p class="m-checkoutListItem">
						<span>@lang('payment.cart-coupon-label')</span>
						<span class="m-checkoutListItem__value">
						-{{$coupon->is_percentage
						? trans('payment.voucher-percentage', ['value' => $coupon->value])
						: trans('payment.voucher-amount', ['value' => $coupon->value])}}
					</span>
					</p>
				@endif
				<p class="m-checkoutTotalAmount">
					<span>@lang('payment.cart-price-label')</span>
					@if(!empty($coupon))
						<span>
							<span class="m-checkoutTotalAmount__preCoupon">
								@lang('payment.cart-price-value', ['value' => $productPrice])
							</span>
							<span class="m-checkoutTotalAmount__final">@lang('payment.cart-price-value', ['value' => $productPriceWithCoupon])</span>
						</span>
					@else
						<span class="m-checkoutTotalAmount__final">@lang('payment.cart-price-value', ['value' => $productPrice])</span>
					@endif
				</p>
			</section>
		@else
			<span>@lang('payment.cart-empty')</span>
		@endif
	</div>
</div>
