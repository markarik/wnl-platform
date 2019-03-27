<div class="o-stepper">
	@foreach([
		['text' => trans('payment.stepper-account'), 'route' => route('payment-account')],
		['text' => trans('payment.stepper-personal-data'), 'route' => route('payment-personal-data')],
		['text' => trans('payment.stepper-confirm-order'), 'route' => route('payment-confirm-order')]
	] as $step)
		@php
			$isDone = $loop->index < $currentStep;
		@endphp
		<div class="o-stepper__step {{$loop->index === $currentStep ? '-active' : ''}} {{$isDone ? '-done' : ''}}">
			<a {{$isDone ? 'href=' . $step['route'] : ''}}>{{$loop->index + 1}}. {{$step['text']}}</a>
		</div>
	@endforeach
</div>
