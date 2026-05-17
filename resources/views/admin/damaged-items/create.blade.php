@extends('admin.layouts.app')

@section('page-title', $damagedItem ? 'Edit Damaged Item' : 'Add Damaged Item')
@section('page-subtitle', 'Handle damaged stock safely')

@section('content')
@include('admin.stocks.partials.form-style')
<div class="form-card">
    <div class="form-card-header"><h4>{{ $damagedItem ? 'Edit Damaged Item' : 'Add Damaged Item' }}</h4><p>{{ $damagedItem ? 'Only notes and reason are editable after stock has been moved.' : 'Choose how damaged stock should be handled.' }}</p></div>
    <div class="form-body">
        <form method="POST" action="{{ $damagedItem ? route('admin.damaged-items.update',$damagedItem) : route('admin.damaged-items.store') }}">
            @csrf
            @if($damagedItem)@method('PUT')@endif
            <div class="row g-4">
                @if(!$damagedItem)
                    @include('admin.stocks.partials.variant-select')
                    <div class="col-md-6"><label class="form-label">Quantity <span class="text-danger">*</span></label><input type="number" min="1" name="quantity" value="{{ old('quantity',1) }}" class="form-control theme-control" required>@error('quantity')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Action Type <span class="text-danger">*</span></label><select name="action_type" class="form-select theme-control" required><option value="">Select Action</option><option value="reduce_from_stock" @selected(old('action_type')==='reduce_from_stock')>Reduce From Stock</option><option value="move_to_discount" @selected(old('action_type')==='move_to_discount')>Move To Discount</option><option value="return_to_supplier" @selected(old('action_type')==='return_to_supplier')>Return To Supplier</option></select>@error('action_type')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Supplier</label><select name="supplier_id" class="form-select theme-control"><option value="">Select Supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id')==$supplier->id)>{{ $supplier->supplier_name }}</option>@endforeach</select>@error('supplier_id')<div class="error-msg">{{ $message }}</div>@enderror</div>
                @else
                    <div class="col-md-12"><div class="stock-preview">{{ $damagedItem->variant->product->product_name ?? 'N/A' }} - {{ $damagedItem->variant->variant_name ?? 'N/A' }} | Quantity: {{ $damagedItem->quantity }} | Action: {{ str_replace('_',' ',ucfirst($damagedItem->action_type)) }}</div></div>
                @endif
                <div class="col-md-12"><label class="form-label">Damage Reason <span class="text-danger">*</span></label><textarea name="damage_reason" rows="4" class="form-control theme-control" required>{{ old('damage_reason', $damagedItem->damage_reason ?? '') }}</textarea>@error('damage_reason')<div class="error-msg">{{ $message }}</div>@enderror</div>
                <div class="col-md-12"><label class="form-label">Notes</label><textarea name="notes" rows="3" class="form-control theme-control">{{ old('notes', $damagedItem->notes ?? '') }}</textarea>@error('notes')<div class="error-msg">{{ $message }}</div>@enderror</div>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn-save"><i class="bi bi-check-circle me-1"></i>{{ $damagedItem ? 'Update Record' : 'Save Damaged Item' }}</button><a href="{{ route('admin.damaged-items.index') }}" class="btn-cancel">Back to Damaged Items</a></div>
        </form>
    </div>
</div>
@if(!$damagedItem)@include('admin.stocks.partials.variant-script')@endif
@endsection
