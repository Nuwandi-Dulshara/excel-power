@extends('admin.layouts.app')

@section('page-title', 'Edit Discounted Product')
@section('page-subtitle', 'Update discounted product details')

@section('content')

@include('admin.discounted-products.form', [
    'discountedProduct' => $discountedProduct,
    'formAction' => route('admin.discounted-products.update', $discountedProduct->id),
    'formMethod' => 'PUT',
    'buttonText' => 'Update Discounted Product'
])

@endsection