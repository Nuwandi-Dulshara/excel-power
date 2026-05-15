<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $categories = ProductCategory::query()
            ->whereNull('parent_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('category_name', 'like', "%{$search}%")
                        ->orWhere('category_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalCategories = ProductCategory::whereNull('parent_id')->count();
        $activeCategories = ProductCategory::whereNull('parent_id')->where('status', 'active')->count();
        $inactiveCategories = ProductCategory::whereNull('parent_id')->where('status', 'inactive')->count();

        return view('admin.product-categories.index', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'inactiveCategories',
            'search',
            'status'
        ));
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create()
    {
        return view('admin.product-categories.create');
    }

    /**
     * Store a newly created product category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:255',
                'unique:product_categories,category_name',
            ],
            'category_code' => [
                'required',
                'string',
                'max:50',
                'unique:product_categories,category_code',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $validated['parent_id'] = null;

        ProductCategory::create($validated);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $productCategory)
    {
        return view('admin.product-categories.edit', compact('productCategory'));
    }

    /**
     * Update the specified product category.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'category_name')->ignore($productCategory->id),
            ],
            'category_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_categories', 'category_code')->ignore($productCategory->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $validated['parent_id'] = null;

        $productCategory->update($validated);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    /**
     * Remove the specified product category.
     */
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }
}
