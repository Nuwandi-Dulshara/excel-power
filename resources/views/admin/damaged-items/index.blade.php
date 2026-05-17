@extends('admin.layouts.app')

@section('page-title', 'Damaged Items')
@section('page-subtitle', 'Record and review damaged stock')

@section('content')
<style>
.toolbar-card,.table-card{background:#fff;border:1px solid #fde68a;border-radius:22px;box-shadow:0 12px 30px rgba(15,23,42,.06)}.toolbar-card{padding:24px}.table-card{overflow:hidden}.btn-theme{background:linear-gradient(135deg,#b91c1c,#7f1d1d);color:#fff;border:0;border-radius:13px;padding:11px 16px;font-weight:800;text-decoration:none}.filter-control{border-radius:13px;border:1px solid #fde68a;padding:11px 14px;font-weight:600;color:#7f1d1d;background:#fffbeb}.table{margin-bottom:0}.table thead th{background:#fffbeb;color:#7f1d1d;font-size:.76rem;text-transform:uppercase;font-weight:900;border-bottom:1px solid #fde68a;padding:15px}.table tbody td{padding:15px;vertical-align:middle;color:#7f1d1d;font-weight:600;border-color:#fff7ed}.badge-soft{background:#fff7ed;color:#7f1d1d;padding:7px 11px;border-radius:999px;font-weight:800;font-size:.74rem}.action-btn{width:38px;height:38px;border-radius:12px;border:0;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;margin-right:5px}.edit-btn{background:rgba(185,28,28,.12);color:#b91c1c}.delete-btn{background:rgba(239,68,68,.12);color:#ef4444}.empty-box{padding:46px 20px;text-align:center;color:#7c2d12}.alert-success-theme{border:0;border-radius:16px;background:#dcfce7;color:#166534;font-weight:700;padding:14px 18px}
</style>

@if(session('success'))<div class="alert alert-success-theme mb-4"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>@endif

<div class="toolbar-card mb-4">
    <div class="d-flex justify-content-between flex-column flex-lg-row gap-3 mb-3">
        <div><h4 class="fw-bold mb-1">Damaged Items List</h4><p class="text-muted mb-0">Track damaged quantities and selected action.</p></div>
        <a href="{{ route('admin.damaged-items.create') }}" class="btn-theme align-self-start"><i class="bi bi-plus-circle me-1"></i>Add Damaged Item</a>
    </div>
    <form method="GET" action="{{ route('admin.damaged-items.index') }}"><div class="row g-3"><div class="col-md-8"><input name="search" value="{{ request('search') }}" class="form-control filter-control" placeholder="Search product, variant, reason"></div><div class="col-md-3"><select name="action_type" class="form-select filter-control"><option value="">All Actions</option><option value="reduce_from_stock" @selected(request('action_type')==='reduce_from_stock')>Reduce From Stock</option><option value="move_to_discount" @selected(request('action_type')==='move_to_discount')>Move To Discount</option><option value="return_to_supplier" @selected(request('action_type')==='return_to_supplier')>Return To Supplier</option></select></div><div class="col-md-1"><button class="btn-theme w-100"><i class="bi bi-search"></i></button></div></div></form>
</div>

<div class="table-card">
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>#</th><th>Product</th><th>Variant</th><th>Qty</th><th>Reason</th><th>Action</th><th>Supplier</th><th>Created By</th><th class="text-end">Action</th></tr></thead><tbody>
        @forelse($damagedItems as $item)
        <tr><td>{{ $damagedItems->firstItem()+$loop->index }}</td><td>{{ $item->variant->product->product_name ?? 'N/A' }}</td><td>{{ $item->variant->variant_name ?? 'N/A' }}</td><td>{{ $item->quantity }}</td><td>{{ $item->damage_reason }}</td><td><span class="badge-soft">{{ str_replace('_',' ',ucfirst($item->action_type)) }}</span></td><td>{{ $item->supplier->supplier_name ?? 'N/A' }}</td><td>{{ $item->createdBy->name ?? 'N/A' }}</td><td class="text-end"><a href="{{ route('admin.damaged-items.edit',$item) }}" class="action-btn edit-btn"><i class="bi bi-pencil-square"></i></a><form action="{{ route('admin.damaged-items.destroy',$item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this damaged item record?');">@csrf @method('DELETE')<button class="action-btn delete-btn"><i class="bi bi-trash3"></i></button></form></td></tr>
        @empty
        <tr><td colspan="9"><div class="empty-box"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No damaged items found.</div></td></tr>
        @endforelse
    </tbody></table></div>
    @if($damagedItems->hasPages())<div class="p-3 border-top">{{ $damagedItems->links() }}</div>@endif
</div>
@endsection
