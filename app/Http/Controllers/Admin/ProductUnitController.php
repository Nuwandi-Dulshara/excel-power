<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductUnitController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        $status = $request->status;

        $units = ProductUnit::query()
            ->when($search, function ($query) use ($search) {
                $query->where('unit_name', 'like', '%' . $search . '%')
                    ->orWhere('short_code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalUnits = ProductUnit::count();
        $activeUnits = ProductUnit::where('status', 'active')->count();
        $inactiveUnits = ProductUnit::where('status', 'inactive')->count();

        return view('admin.product-units.index', compact(
            'units',
            'totalUnits',
            'activeUnits',
            'inactiveUnits',
            'search',
            'status'
        ));
    }

    public function create(): View
    {
        return view('admin.product-units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_name' => ['required', 'string', 'max:255', 'unique:product_units,unit_name'],
            'short_code' => ['required', 'string', 'max:50', 'unique:product_units,short_code'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        ProductUnit::create([
            'unit_name' => $request->unit_name,
            'short_code' => $request->short_code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.product-units.index')
            ->with('success', 'Product unit created successfully.');
    }

    public function edit(ProductUnit $productUnit): View
    {
        return view('admin.product-units.edit', compact('productUnit'));
    }

    public function update(Request $request, ProductUnit $productUnit): RedirectResponse
    {
        $request->validate([
            'unit_name' => ['required', 'string', 'max:255', 'unique:product_units,unit_name,' . $productUnit->id],
            'short_code' => ['required', 'string', 'max:50', 'unique:product_units,short_code,' . $productUnit->id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $productUnit->update([
            'unit_name' => $request->unit_name,
            'short_code' => $request->short_code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.product-units.index')
            ->with('success', 'Product unit updated successfully.');
    }

    public function destroy(ProductUnit $productUnit): RedirectResponse
    {
        $productsCount = $productUnit->products()->count();
        $variantsCount = $productUnit->variants()->count();

        if ($productsCount > 0 || $variantsCount > 0) {
            return redirect()
                ->route('admin.product-units.index')
                ->with('error', "Cannot delete this product unit because it is used by {$productsCount} product(s) and {$variantsCount} sub product(s).");
        }

        $productUnit->delete();

        return redirect()
            ->route('admin.product-units.index')
            ->with('success', 'Product unit deleted successfully.');
    }
}
