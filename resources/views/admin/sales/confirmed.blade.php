@extends('admin.layouts.app')

@section('page-title', 'Confirmed Bills')
@section('page-subtitle', 'View completed bills and open printable invoices')

@section('content')
@include('admin.sales.partials.saved-list', [
    'sales' => $sales,
    'emptyText' => 'No confirmed bills found.',
    'mode' => 'confirmed',
])
@endsection
