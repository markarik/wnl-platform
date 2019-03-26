@php
	/**
   * @var App\Models\OrderInstalment[] $instalments
   * @var App\Models\Order $order
   */
@endphp
<h3 class="o-checkoutModal__header">
	@lang('payment.instalments-modal-header')
	<i class="a-icon fa-info-circle -medium"></i>
</h3>
<p class="o-checkoutInstalmentsModal__intro">@lang('payment.instalments-modal-intro')</p>
@foreach ($instalments as $instalment)
	<div class="o-checkoutInstalmentsModal__instalment" data-instalment-number="{{$instalment->order_number}}">
		<p class="o-checkoutInstalmentsModal__instalmentHeader">@lang('payment.instalments-modal-instalment-header', ['number' => $instalment->order_number])</p>
		<div class="m-checkoutListItem">
			<span>@lang('payment.instalments-modal-due-date-label')</span>
			<span class="m-checkoutListItem__value">
				@if ($instalment->order_number === 1)
					@lang('payment.instalments-modal-due-date-first-instalment')
				@else
					@lang('payment.instalments-modal-due-date', [
						'date' => '<span data-date-format="D MMMM Y" data-timestamp="' . $instalment->due_date->timestamp . '">' . $instalment->due_date->format('d.m.Y') . '</span>'
					])
				@endif
			</span>
		</div>
		<div class="m-checkoutListItem">
			<span>@lang('payment.instalments-modal-amount-label')</span>
			<span class="-textLight">@lang('payment.instalments-modal-amount', ['amount' => $instalment->amount])</span>
		</div>
	</div>
@endforeach
<div class="m-checkoutTotalAmount">
	<span>@lang('payment.instalments-modal-total-amount')</span>
	<span class="m-checkoutTotalAmount__final">@lang('payment.instalments-modal-amount', ['amount' => $order->total_with_coupon])</span>
</div>
<p class="o-checkoutInstalmentsModal__tou">@lang('payment.instalments-modal-tou', [
	'pricing-link-href' => trans('payment.pricing-link-href'),
	'tou-link-href' => trans('payment.tou-link-href'),
])</p>
<div class="-textCenter">
	<button id="instalments-modal-closer" class="a-button -big">
		@lang('payment.instalments-modal-button')
	</button>
</div>
