@php
	/**
   * @var App\Models\OrderInstalment[] $instalments
   * @var App\Models\Order $order
   */
@endphp
<h3 class="o-checkoutModal__header">
	@lang('payment.instalments-modal-header')
	<i class="a-icon fa-info-circle"></i>
</h3>
<p class="o-checkoutModal__intro -textMinus1 -stormGray">@lang('payment.instalments-modal-intro')</p>
@foreach ($instalments as $instalment)
	<div class="o-checkoutModal__instalment">
		<p class="o-checkoutModal__instalmentHeader">@lang('payment.instalments-modal-instalment-header', ['number' => $instalment->order_number])</p>
		<div class="o-checkoutModal__instalmentDetail -centeredSpread">
			<span>@lang('payment.instalments-modal-due-date-label')</span>
			<span class="-textLight">
				@if ($instalment->order_number === 1)
					@lang('payment.instalments-modal-due-date-first-instalment')
				@else
					@lang('payment.instalments-modal-due-date', [
						'date' => '<span data-date-format="D MMMM Y" data-timestamp="' . $instalment->due_date->timestamp . '">' . $instalment->due_date->format('d.m.Y') . '</span>'
					])
				@endif
			</span>
		</div>
		<div class="o-checkoutModal__instalmentDetail -centeredSpread">
			<span>@lang('payment.instalments-modal-amount-label')</span>
			<span class="-textLight">@lang('payment.instalments-modal-amount', ['amount' => $instalment->amount])</span>
		</div>
	</div>
@endforeach
<div class="o-checkoutModal__instalmentsTotalAmount -centeredSpread -textMinus1">
	<span>@lang('payment.instalments-modal-total-amount')</span>
	<span class="-textPlus1">@lang('payment.instalments-modal-amount', ['amount' => $order->total_with_coupon])</span>
</div>
<p class="o-checkoutModal__tou -textMinus2 -stormGray">@lang('payment.instalments-modal-tou', [
	'pricing-link-href' => trans('payment.pricing-link-href'),
	'tou-link-href' => trans('payment.tou-link-href'),
])</p>
<button id="instalments-modal-closer" class="button is-primary is-wide">
	@lang('payment.instalments-modal-button')
</button>
