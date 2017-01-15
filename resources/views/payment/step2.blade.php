@extends('layouts.payment')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-xs-offset-0 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

				<div class="row">
					<div class="col-xs-12">
						<h2 class="text-center">Świetny wybór!</h2>
						<div class="alert alert-success text-center">
							Zapisujesz się na kurs stacjonarny w cenie <strong>2000zł</strong> brutto.
						</div>
					</div>
				</div>

				{!! form_start($form)  !!}

				<div class="row">
					<div class="col-xs-12">
						<h2 class="text-center">To co, zakładamy konto?</h2>
						<p class="lead">Najpierw prosimy o podanie maila i hasła, których będziesz używać do logowania.</p>

						<div class="form-group">
							{!! form_widget($form->email) !!}
							{!! form_errors($form->email) !!}

							{!! form_widget($form->password) !!}
							{!! form_errors($form->password) !!}

							{!! form_widget($form->password_confirmation) !!}
							{!! form_errors($form->password_confirmation) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-md-6">
						{!! form_widget($form->first_name) !!}
						{!! form_errors($form->first_name) !!}
					</div>
					<div class="col-xs-12 col-md-6">
						{!! form_widget($form->last_name) !!}
						{!! form_errors($form->last_name) !!}
					</div>
					<div class="col-xs-12">
						{!! form_widget($form->address) !!}
						{!! form_errors($form->address) !!}
					</div>
					<div class="col-xs-12 col-md-3">
						{!! form_widget($form->zip) !!}
						{!! form_errors($form->zip) !!}
					</div>
					<div class="col-xs-12 col-md-9">
						{!! form_widget($form->city) !!}
						{!! form_errors($form->city) !!}
					</div>
				</div>

				{!! form_widget($form->privacy_policy) !!}
				{!! form_errors($form->privacy_policy) !!}

				{!! form_widget($form->newsletter) !!}
				{!! form_errors($form->newsletter) !!}

				<button>Dalej</button>

				{!! form_end($form, false)  !!}

		</div>
	</div>
</div>
@endsection