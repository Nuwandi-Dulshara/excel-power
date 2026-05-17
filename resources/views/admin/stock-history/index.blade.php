@extends('admin.layouts.app')

@section('page-title', 'Stock History')
@section('page-subtitle', 'All stock movements and audit trail')

@section('content')
<style>
.toolbar-card,.table-card{background:#fff;border:1px solid #fde68a;border-radius:22px;box-shadow:0 12px 30px rgba(15,23,42,.06)}.toolbar-card{padding:24px}.table-card{overflow:hidden}.btn-theme{background:linear-gradient(135deg,#b91c1c,#7f1d1d);color:#fff;border:0;border-radius:13px;padding:11px 16px;font-weight:800}.filter-control{border-radius:13px;border:1px solid #fde68a;padding:11px 14px;font-weight:600;color:#7f1d1d;background:#fffbeb}.table{margin-bottom:0}.table thead th{background:#fffbeb;color:#7f1d1d;font-size:.76rem;text-transform:uppercase;font-weight:900;border-bottom:1px solid #fde68a;padding:15px}.table tbody td{padding:15px;vertical-align:middle;color:#7f1d1d;font-weight:600;border-color:#fff7ed}.badge-move{background:#fff7ed;color:#7f1d1d;padding:7px 11px;border-radius:999px;font-weight:800;font-size:.74rem}.empty-box{padding:46px 20px;text-align:center;color:#7c2d12}
</style>

<div class="toolbar-card mb-4">
    <div class="d-flex justify-content-between flex-column flex-lg-row gap-3 mb-3">
        <div><h4 class="fw-bold mb-1">Movement History</h4><p class="text-muted mb-0">Filter product movement records by type, supplier, date, or user.</p></div>
        <a href="{{ route('admin.stocks.index') }}" class="btn-theme text-decoration-none align-self-start"><i class="bi bi-arrow-left me-1"></i> Back to Stocks</a>
    </div>
    <form method="GET" action="{{ route('admin.stock-history.index') }}">
        <div class="row g-3">
            <div class="col-lg-3"><input name="search" value="{{ request('search') }}" class="form-control filter-control" placeholder="Product, variant, barcode, SKU"></div>
            <div class="col-lg-2"><select name="movement_type" class="form-select filter-control"><option value="">All Types</option>@foreach($movementTypes as $type)<option value="{{ $type }}" @selected(request('movement_type')===$type)>{{ str_replace('_',' ',ucfirst($type)) }}</option>@endforeach</select></div>
            <div class="col-lg-2"><select name="supplier_id" class="form-select filter-control"><option value="">All Suppliers</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(request('supplier_id')==$supplier->id)>{{ $supplier->supplier_name }}</option>@endforeach</select></div>
            <div class="col-lg-2"><select name="created_by" class="form-select filter-control"><option value="">All Users</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('created_by')==$user->id)>{{ $user->name }}</option>@endforeach</select></div>
            <div class="col-lg-1"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control filter-control"></div>
            <div class="col-lg-1"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control filter-control"></div>
            <div class="col-lg-1"><button class="btn-theme w-100"><i class="bi bi-search"></i></button></div>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Date</th><th>Product</th><th>Variant/Sub Product</th><th>Barcode/SKU</th><th>Movement Type</th><th>Qty</th><th>Previous</th><th>New</th><th>Supplier</th><th>Reason</th><th>Created By</th></tr></thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date?->format('Y-m-d') }}</td>
                    <td>{{ $movement->variant->product->product_name ?? 'N/A' }}</td>
                    <td>{{ $movement->variant->variant_name ?? 'N/A' }}</td>
                    <td>{{ $movement->variant->barcode ?? 'N/A' }}<br><small>{{ $movement->variant->sku ?? 'N/A' }}</small></td>
                    <td><span class="badge-move">{{ str_replace('_',' ',ucfirst($movement->movement_type)) }}</span></td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->previous_stock }}</td>
                    <td>{{ $movement->new_stock }}</td>
                    <td>{{ $movement->supplier->supplier_name ?? 'N/A' }}</td>
                    <td>{{ $movement->reason ?? 'N/A' }}</td>
                    <td>{{ $movement->createdBy->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="11"><div class="empty-box"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No stock movements found.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())<div class="p-3 border-top">{{ $movements->links() }}</div>@endif
</div>
@endsection
