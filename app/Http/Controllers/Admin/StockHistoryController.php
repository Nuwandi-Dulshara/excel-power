<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $movements = StockMovement::with(['variant.product', 'supplier', 'createdBy'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('variant', function ($q) use ($search) {
                    $q->where('variant_name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($product) => $product->where('product_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->input('movement_type'), fn ($query, $type) => $query->where('movement_type', $type))
            ->when($request->input('supplier_id'), fn ($query, $supplierId) => $query->where('supplier_id', $supplierId))
            ->when($request->input('created_by'), fn ($query, $userId) => $query->where('created_by', $userId))
            ->when($request->input('date_from'), fn ($query, $date) => $query->whereDate('movement_date', '>=', $date))
            ->when($request->input('date_to'), fn ($query, $date) => $query->whereDate('movement_date', '<=', $date))
            ->latest('movement_date')
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.stock-history.index', [
            'movements' => $movements,
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
            'users' => User::orderBy('name')->get(),
            'movementTypes' => StockMovement::TYPES,
        ]);
    }
}
