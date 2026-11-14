@extends('errors::minimal')

@section('title', __('Page not found'))
@section('code', '404')
@section('message', __('There is|nothing here.'))
@section('description')
    {!! __('The page you are looking for does not exist. Or not yet: if you really needed it, <em>I can build it.</em>') !!}
@endsection
@section('cta', __('Let\'s build it'))
