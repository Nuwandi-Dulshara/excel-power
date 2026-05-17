<style>
.table-card {
    background: white;
    border: 1px solid #fde68a;
    border-radius: 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
}

.table thead th {
    background: #fff7ed;
    color: #7f1d1d;
    font-size: .76rem;
    text-transform: uppercase;
    font-weight: 900;
    border-bottom: 1px solid #fde68a;
}

.btn-open {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border-radius: 12px;
    padding: 8px 12px;
    text-decoration: none;
    font-weight: 850;
}

.btn-open:hover {
    color: white;
}

.btn-soft-action {
    background: #fff7ed;
    color: #7f1d1d;
    border: none;
    border-radius: 12px;
    padding: 8px 12px;
    text-decoration: none;
    font-weight: 850;
}

.btn-delete-action {
    background: #fee2e2;
    color: #991b1b;
    border: none;
    border-radius: 12px;
    padding: 8px 12px;
    font-weight: 850;
}
</style>

<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <div></div>
    <a href="{{ route('admin.sales.index') }}" class="btn-open">
        <i class="bi bi-plus-circle me-1"></i> Get Another Bill
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice Number</th>
                    <th>Created</th>
                    <th>Cashier</th>
                    <th>Items</th>
                    <th>Grand Total</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $savedSale)
                <tr>
                    <td>{{ $sales->firstItem() + $loop->index }}</td>
                    <td>{{ $savedSale->invoice_no ?? 'N/A' }}</td>
                    <td>{{ $savedSale->created_at->format('Y-m-d h:i A') }}</td>
                    <td>{{ $savedSale->cashier->name ?? 'N/A' }}</td>
                    <td>{{ $savedSale->items_count ?? $savedSale->items->count() }}</td>
                    <td class="fw-bold text-danger">Rs. {{ number_format($savedSale->grand_total, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ ($mode ?? null) === 'confirmed' ? route('admin.sales.print', $savedSale) : route('admin.sales.edit', $savedSale) }}"
                            class="btn-open">
                            Open
                        </a>

                        @if(($mode ?? null) !== 'confirmed')
                        <form method="POST" action="{{ route('admin.sales.cancel', $savedSale) }}" class="d-inline"
                            onsubmit="return confirm('Delete this bill?');">
                            @csrf
                            <button type="submit" class="btn-delete-action">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        {{ $emptyText }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sales->hasPages())
    <div class="p-3">
        {{ $sales->links() }}
    </div>
    @endif
</div>
