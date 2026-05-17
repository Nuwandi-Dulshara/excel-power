@extends('admin.layouts.app')

@section('page-title', 'Held Bills')
@section('page-subtitle', 'Open held bills and complete them later')

@section('content')
@include('admin.sales.partials.saved-list', [
    'sales' => $sales,
    'emptyText' => 'No held bills found.',
    'mode' => 'held',
])
@endsection
