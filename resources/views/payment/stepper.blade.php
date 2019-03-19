<div class="o-stepper">
	@foreach([
		['text' => 'Konto', 'route' => route('payment-account')],
		['text' => 'Dane osobowe', 'route' => route('payment-personal-data')],
		['text' => 'Podsumowanie', 'route' => route('payment-confirm-order')]
	] as $step)
		@php
			$isDone = $loop->index < $currentStep;
		@endphp
		<div class="o-stepper__step {{$loop->index === $currentStep ? '-isActive' : ''}} {{$isDone ? '-isDone' : ''}}">
			<a {{$isDone ? 'href=' . $step['route'] : ''}}>{{$loop->index + 1}}. {{$step['text']}}</a>
		</div>
	@endforeach
</div>
