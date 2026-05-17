@extends('admin.layouts.app')

@section('page-title', $returnedItem ? 'Edit Returned Item' : 'Add Returned Item')
@section('page-subtitle', 'Record customer or supplier returns')

@section('content')
@include('admin.stocks.partials.form-style')
<div class="form-card">
    <div class="form-card-header"><h4>{{ $returnedItem ? 'Edit Returned Item' : 'Add Returned Item' }}</h4><p>{{ $returnedItem ? 'Only notes and reason are editable after stock has been moved.' : 'Choose the return condition and what should happen to stock.' }}</p></div>
    <div class="form-body">
        <form method="POST" action="{{ $returnedItem ? route('admin.returned-items.update',$returnedItem) : route('admin.returned-items.store') }}">
            @csrf
            @if($returnedItem)@method('PUT')@endif
            <div class="row g-4">
                @if(!$returnedItem)
                    @include('admin.stocks.partials.variant-select')
                    <div class="col-md-6"><label class="form-label">Sale</label><select name="sale_id" class="form-select theme-control"><option value="">Select Sale</option>@foreach($sales as $sale)<option value="{{ $sale->id }}" @selected(old('sale_id')==$sale->id)>{{ $sale->invoice_no ?? ('Sale #'.$sale->id) }}</option>@endforeach</select>@error('sale_id')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Sale Item</label><select name="sale_item_id" class="form-select theme-control"><option value="">Select Sale Item</option>@foreach($saleItems as $saleItem)<option value="{{ $saleItem->id }}" @selected(old('sale_item_id')==$saleItem->id)>{{ $saleItem->sale->invoice_no ?? ('Sale #'.$saleItem->sale_id) }} - {{ $saleItem->variant_name }}</option>@endforeach</select>@error('sale_item_id')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Quantity <span class="text-danger">*</span></label><input type="number" min="1" name="quantity" value="{{ old('quantity',1) }}" class="form-control theme-control" required>@error('quantity')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Return Type <span class="text-danger">*</span></label><select name="return_type" class="form-select theme-control" required><option value="">Select Type</option><option value="customer_return" @selected(old('return_type')==='customer_return')>Customer Return</option><option value="supplier_return" @selected(old('return_type')==='supplier_return')>Supplier Return</option></select>@error('return_type')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Condition <span class="text-danger">*</span></label><select name="condition" class="form-select theme-control" required><option value="">Select Condition</option><option value="good" @selected(old('condition')==='good')>Good</option><option value="damaged" @selected(old('condition')==='damaged')>Damaged</option><option value="expired" @selected(old('condition')==='expired')>Expired</option></select>@error('condition')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Action Type <span class="text-danger">*</span></label><select name="action_type" class="form-select theme-control" required><option value="">Select Action</option><option value="add_back_to_stock" @selected(old('action_type')==='add_back_to_stock')>Add Back To Stock</option><option value="move_to_damage" @selected(old('action_type')==='move_to_damage')>Move To Damage</option><option value="move_to_discount" @selected(old('action_type')==='move_to_discount')>Move To Discount</option><option value="return_to_supplier" @selected(old('action_type')==='return_to_supplier')>Return To Supplier</option></select>@error('action_type')<div class="error-msg">{{ $message }}</div>@enderror</div>
                @else
                    <div class="col-md-12"><div class="stock-preview">{{ $returnedItem->variant->product->product_name ?? 'N/A' }} - {{ $returnedItem->variant->variant_name ?? 'N/A' }} | Quantity: {{ $returnedItem->quantity }} | Action: {{ str_replace('_',' ',ucfirst($returnedItem->action_type)) }}</div></div>
                @endif
                <div class="col-md-12"><label class="form-label">Reason</label><textarea name="reason" rows="3" class="form-control theme-control">{{ old('reason', $returnedItem->reason ?? '') }}</textarea>@error('reason')<div class="error-msg">{{ $message }}</div>@enderror</div>
                <div class="col-md-12"><label class="form-label">Notes</label><textarea name="notes" rows="3" class="form-control theme-control">{{ old('notes', $returnedItem->notes ?? '') }}</textarea>@error('notes')<div class="error-msg">{{ $message }}</div>@enderror</div>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn-save"><i class="bi bi-check-circle me-1"></i>{{ $returnedItem ? 'Update Record' : 'Save Returned Item' }}</button><a href="{{ route('admin.returned-items.index') }}" class="btn-cancel">Back to Returned Items</a></div>
        </form>
    </div>
</div>
@if(!$returnedItem)@include('admin.stocks.partials.variant-script')@endif
@endsection
