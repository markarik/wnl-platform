
<div class="card column cart{{!empty($coupon) ? ' has-coupon' : ''}}">
	@if(!empty($productName))
		<header class="card-header">
			<h3 class="card-header-title">@lang('payment.cart-header')</h3>
		</header>
		<div class="card-content">
			<p>
				<h4>{{$productName}}</h4>
				@if (!empty($productAccessEnd))
					@lang('payment.cart-access-info', ['date' => $productAccessEnd->format('d.m.Y')])
				@endif
			</p>
			<p>
				<span>@lang('payment.cart-shipment-label')</span>
				<span>@lang('payment.cart-shipment-value')</span>
			</p>
			@if(!empty($coupon))
				<p>
					<span>@lang('payment.cart-coupon-label')</span>
					<span>
						-{{$coupon->is_percentage
						? trans('payment.voucher-percentage', ['value' => $coupon->value])
						: trans('payment.voucher-amount', ['value' => $coupon->value])}}
					</span>
				</p>
				<p>
					<span>@lang('payment.cart-price-label')</span>
					<span class="strikethrough">
						@lang('payment.cart-price-value', ['value' => $productPrice])
					</span>
					<span>@lang('payment.cart-price-value', ['value' => $productPriceWithCoupon])</span>
				</p>
			@else
				<span>@lang('payment.cart-price-label')</span>
				<span>@lang('payment.cart-price-value', ['value' => $productPrice])</span>
			@endif
		</div>
	@else
		<span>@lang('payment.cart-empty')</span>
	@endif
</div>
