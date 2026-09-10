@extends("components.layout")

@section("title", "Dashboard")

@section("content")
    <h1>Welcome {{auth()->user()->name}}</h1>
@endSection
