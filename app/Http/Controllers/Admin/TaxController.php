<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $taxes = Tax::query()
            ->when($search, function ($query) use ($search) {
                $query->where('tax_name', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalTaxes = Tax::count();
        $activeTaxes = Tax::where('status', 'active')->count();
        $inactiveTaxes = Tax::where('status', 'inactive')->count();

        return view('admin.taxes.index', compact(
            'taxes',
            'totalTaxes',
            'activeTaxes',
            'inactiveTaxes',
            'search',
            'status'
        ));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax_name' => ['required', 'string', 'max:255', 'unique:taxes,tax_name'],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'tax_type' => ['required', Rule::in(['inclusive', 'exclusive'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string'],
        ]);

        Tax::create($validated);

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'tax_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('taxes', 'tax_name')->ignore($tax->id),
            ],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'tax_type' => ['required', Rule::in(['inclusive', 'exclusive'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string'],
        ]);

        $tax->update($validated);

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}