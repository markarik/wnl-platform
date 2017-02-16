@extends('layouts.guest')

@section('content')

<form action="{{ route('admin-upload-slides-post') }}" method="post">
	{!! csrf_field() !!}
	<textarea name="slides" rows="10" cols="200" placeholder="Kod prezentacji"></textarea>
	<br>
	<input type="submit" value="Parsuj parserze!" class="btn btn-primary">
</form>

@endsection