<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $subCategory = $request->input('sub_category');
        $productUnitId = $request->input('product_unit_id');
        $supplierId = $request->input('supplier_id');
        $status = $request->input('status');

        $products = Product::with(['category', 'unit', 'supplier'])
            ->withCount('variants')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($subCategory, function ($query) use ($subCategory) {
                $query->where('sub_category', 'like', "%{$subCategory}%");
            })
            ->when($productUnitId, function ($query) use ($productUnitId) {
                $query->where('product_unit_id', $productUnitId);
            })
            ->when($supplierId, function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $inactiveProducts = Product::where('status', 'inactive')->count();
        $totalVariants = ProductVariant::count();
        $activeVariants = ProductVariant::where('status', 'active')->count();
        $inactiveVariants = ProductVariant::where('status', 'inactive')->count();

        $categories = ProductCategory::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('category_name')
            ->get();

        $units = ProductUnit::where('status', 'active')
            ->orderBy('unit_name')
            ->get();

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('supplier_name')
            ->get();

        return view('admin.products.index', compact(
            'products',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'totalVariants',
            'activeVariants',
            'inactiveVariants',
            'categories',
            'units',
            'suppliers',
            'search',
            'categoryId',
            'subCategory',
            'productUnitId',
            'supplierId',
            'status'
        ));
    }

    public function create()
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('category_name')
            ->get();

        $units = ProductUnit::where('status', 'active')
            ->orderBy('unit_name')
            ->get();

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('supplier_name')
            ->get();

        return view('admin.products.create', compact('categories', 'units', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['nullable', 'string', 'max:100', 'unique:products,product_code'],
            'category_id' => [
                'required',
                Rule::exists('product_categories', 'id')->where(function ($query) {
                    $query->whereNull('parent_id')->where('status', 'active');
                }),
            ],
            'sub_category' => ['required', 'string'],
            'product_unit_id' => [
                'required',
                Rule::exists('product_units', 'id')->where('status', 'active'),
            ],
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where('status', 'active'),
            ],
            'brand' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('category_name')
            ->get();

        $units = ProductUnit::where('status', 'active')
            ->orderBy('unit_name')
            ->get();

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('supplier_name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories', 'units', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'product_code')->ignore($product->id),
            ],
            'category_id' => [
                'required',
                Rule::exists('product_categories', 'id')->where(function ($query) {
                    $query->whereNull('parent_id')->where('status', 'active');
                }),
            ],
            'sub_category' => ['required', 'string'],
            'product_unit_id' => [
                'required',
                Rule::exists('product_units', 'id')->where('status', 'active'),
            ],
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where('status', 'active'),
            ],
            'brand' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->variants()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Cannot delete this product because it has sub products.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
