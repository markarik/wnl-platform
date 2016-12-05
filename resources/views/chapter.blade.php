@extends('layouts.app')

@section('content')
    content section

    <h3>{{$chapter->name}}</h3>
    <ul>
        @foreach($chapter->sections as $section)
            <li>{{$section->name}}</li>
        @endforeach
    </ul>
@endsection
