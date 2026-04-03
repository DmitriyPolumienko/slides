@extends('layouts.app')

@section('title', 'Builder - ' . $presentation->title)

@section('content')
@livewire('builder', ['presentation' => $presentation])
@endsection
