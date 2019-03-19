@php
	/**
   * @var App\Models\OrderInstalment[] $instalments
   * @var App\Models\Order $order
   */
@endphp
<h3 class="v-central -centeredSpread">
	@lang('payment.instalments-modal-header') <i class="fa fa-info-circle -cadetBlue"></i>
</h3>
<div>
	<p>@lang('payment.instalments-modal-intro')</p>
	@foreach ($instalments as $instalment)
		<div>
			<h4>@lang('payment.instalments-modal-instalment-header', ['number' => $instalment->order_number])</h4>
			<div>
				<span>@lang('payment.instalments-modal-due-date-label')</span>
				<span>
					@if ($instalment->order_number === 1)
						@lang('payment.instalments-modal-due-date-first-instalment')
					@else
						@lang('payment.instalments-modal-due-date', [
							'date' => '<span data-date-format="D MMMM Y" data-timestamp="' . $instalment->due_date->timestamp . '">' . $instalment->due_date->format('d.m.Y') . '</span>'
						])
					@endif
				</span>
			</div>
			<div>
				<span>@lang('payment.instalments-modal-amount-label')</span>
				<span>@lang('payment.instalments-modal-amount', ['amount' => $instalment->amount])</span>
			</div>
		</div>
	@endforeach
	<div>
		<span>@lang('payment.instalments-modal-total-amount')</span>
		<span>@lang('payment.instalments-modal-amount', ['amount' => $order->total_with_coupon])</span>
	</div>
	<p>@lang('payment.instalments-modal-tou', [
		'pricing-link-href' => trans('payment.pricing-link-href'),
		'tou-link-href' => trans('payment.tou-link-href'),
	])</p>
</div>
<button id="instalments-modal-closer" class="button is-primary is-wide margin top">
	@lang('payment.instalments-modal-button')
</button>
