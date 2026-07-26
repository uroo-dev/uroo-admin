@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
    @livewire('invoice-list')
@endsection
