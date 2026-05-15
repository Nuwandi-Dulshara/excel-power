@extends('admin.layouts.app')

@section('page-title', 'Add Discounted Product')
@section('page-subtitle', 'Create a discounted price for an active sub product')

@section('content')

@include('admin.discounted-products.form', [
'discountedProduct' => null,
'formAction' => route('admin.discounted-products.store'),
'formMethod' => 'POST',
'buttonText' => 'Save Discounted Product'
])

@endsection