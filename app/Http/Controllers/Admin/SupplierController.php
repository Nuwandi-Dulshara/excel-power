<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $paymentTerms = $request->input('payment_terms');
    $balanceFilter = $request->input('balance_filter');

    $suppliers = Supplier::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhere('supplier_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('br_number', 'like', "%{$search}%");
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->when($paymentTerms, function ($query) use ($paymentTerms) {
            $query->where('payment_terms', $paymentTerms);
        })
        ->when($balanceFilter, function ($query) use ($balanceFilter) {
            if ($balanceFilter === 'with_balance') {
                $query->where('opening_balance', '>', 0);
            }

            if ($balanceFilter === 'no_balance') {
                $query->where('opening_balance', '=', 0);
            }
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    $totalSuppliers = Supplier::count();
    $activeSuppliers = Supplier::where('status', 'active')->count();
    $inactiveSuppliers = Supplier::where('status', 'inactive')->count();
    $totalOpeningBalance = Supplier::sum('opening_balance');

    return view('admin.suppliers.index', compact(
        'suppliers',
        'search',
        'status',
        'paymentTerms',
        'balanceFilter',
        'totalSuppliers',
        'activeSuppliers',
        'inactiveSuppliers',
        'totalOpeningBalance'
    ));
}

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_code' => ['required', 'string', 'max:100', 'unique:suppliers,supplier_code'],

            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],

            'address' => ['nullable', 'string'],

            'company_name' => ['nullable', 'string', 'max:255'],
            'br_number' => ['nullable', 'string', 'max:100'],
            'vat_number' => ['nullable', 'string', 'max:100'],

            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        Supplier::create($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('suppliers', 'supplier_code')->ignore($supplier->id),
            ],

            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],

            'address' => ['nullable', 'string'],

            'company_name' => ['nullable', 'string', 'max:255'],
            'br_number' => ['nullable', 'string', 'max:100'],
            'vat_number' => ['nullable', 'string', 'max:100'],

            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        $supplier->update($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}