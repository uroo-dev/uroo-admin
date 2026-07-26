@extends('layouts.app')

@section('title', 'Bookmarks')
@section('page-title', 'Bookmarks')

@section('content')
    @livewire('bookmark-list')
@endsection
