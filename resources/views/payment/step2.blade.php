@extends('layouts.payment')

@section('content')
<div class="container">

    {!! form_start($form)  !!}

    {!! form_widget($form->first_name) !!}
    {!! form_errors($form->first_name) !!}

    {!! form_widget($form->last_name) !!}
    {!! form_errors($form->last_name) !!}

    {!! form_widget($form->address) !!}
    {!! form_errors($form->address) !!}

    {!! form_widget($form->zip) !!}
    {!! form_errors($form->zip) !!}

    {!! form_widget($form->city) !!}
    {!! form_errors($form->city) !!}

    {!! form_widget($form->email) !!}
    {!! form_errors($form->email) !!}

    {!! form_widget($form->password) !!}
    {!! form_errors($form->password) !!}

    {!! form_widget($form->password_confirmation) !!}
    {!! form_errors($form->password_confirmation) !!}

    {!! form_widget($form->privacy_policy) !!}
    {!! form_errors($form->privacy_policy) !!}

    {!! form_widget($form->newsletter) !!}
    {!! form_errors($form->newsletter) !!}

    <button>Dalej</button>

    {!! form_end($form, false)  !!}
</div>
@endsection