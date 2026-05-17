@extends('admin.layouts.app')

@section('page-title', 'Draft Bills')
@section('page-subtitle', 'Open draft bills and confirm them later')

@section('content')
@include('admin.sales.partials.saved-list', [
    'sales' => $sales,
    'emptyText' => 'No draft bills found.',
    'mode' => 'draft',
])
@endsection
