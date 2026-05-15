@extends('admin.layouts.app')

@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Overview of your POS system')

@section('content')

<div class="row g-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-title">Products</div>
            <h3 class="stat-value">0</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-tags"></i>
            </div>
            <div class="stat-title">Categories</div>
            <h3 class="stat-value">0</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-title">Customers</div>
            <h3 class="stat-value">0</h3>
        </div>
    </div>
</div>

<div class="content-card mt-4">
    <h4 class="fw-bold mb-2">Welcome, {{ auth()->user()->name }}</h4>
    <p class="text-muted mb-0">
        This is your admin control panel. Use the sidebar to manage product categories,
        units, suppliers, customers, stocks, and products.
    </p>
</div>

@endsection