@extends('admin.layouts.app')

@section('page-title', 'Manage Customers')
@section('page-subtitle', 'Manage customer details')

@section('content')
<style>
.unit-total-card,
.unit-toolbar-card,
.unit-table-card {
    background: white;
    border: 1px solid #fde68a;
    border-radius: 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06)
}

.unit-total-card {
    background: linear-gradient(135deg, #b91c1c, #d4af37);
    color: white;
    padding: 24px;
    position: relative;
    overflow: hidden
}

.unit-total-card::after {
    content: "";
    position: absolute;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.18);
    right: -60px;
    top: -60px;
    border-radius: 50%
}

.unit-total-icon {
    width: 54px;
    height: 54px;
    background: rgba(255, 255, 255, 0.18);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 16px
}

.unit-total-label {
    font-size: .9rem;
    opacity: .85;
    font-weight: 700
}

.unit-total-value {
    font-size: 2.2rem;
    font-weight: 900;
    margin: 0
}

.unit-toolbar-card,
.unit-table-card {
    padding: 24px
}
</style>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="unit-total-label">Customers</div>
            <h2 class="unit-total-value">0</h2>
        </div>
    </div>
</div>

<div class="unit-toolbar-card mb-4">
    <h4 class="fw-bold mb-1">Manage Customers</h4>
    <p class="text-muted mb-0">Customer management page is connected successfully.</p>
</div>

<div class="unit-table-card">
    <div class="text-center text-muted py-4">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        Customer table will appear here.
    </div>
</div>
@endsection
