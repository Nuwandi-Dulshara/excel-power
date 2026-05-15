<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductUnitController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DiscountedProductController;
use App\Http\Controllers\Admin\TaxController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->can('dashboard.access')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('cashier')) {
            return redirect()->route('cashier.dashboard');
        }

        if ($user->hasRole('developer')) {
            return redirect()->route('developer.dashboard');
        }

        return redirect()->route('login');
    })->name('dashboard');

    Route::get('/cashier/dashboard', [DashboardController::class, 'cashier'])
        ->middleware('role:cashier')
        ->name('cashier.dashboard');

    Route::get('/developer/dashboard', [DashboardController::class, 'developer'])
        ->middleware('role:developer')
        ->name('developer.dashboard');
});

Route::middleware(['auth', 'active.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])
            ->middleware('permission:dashboard.access')
            ->name('dashboard');

        Route::resource('product-categories', ProductCategoryController::class)
            ->except(['show'])
            ->middleware('permission:product_categories.access');

        Route::resource('product-units', ProductUnitController::class)
            ->except(['show'])
            ->middleware('permission:product_units.access');

        Route::resource('suppliers', SupplierController::class)
            ->except(['show'])
            ->middleware('permission:suppliers.access');

        Route::resource('products', ProductController::class)
            ->except(['show'])
            ->middleware('permission:products.access');

        Route::resource('products.variants', ProductVariantController::class)
            ->except(['show'])
            ->parameters([
                'variants' => 'variant',
            ])
            ->middleware('permission:products.access');

            Route::get('discounted-products/variant-details/{variant}', [DiscountedProductController::class, 'variantDetails'])
            ->middleware('permission:discounted_products.access')
            ->name('discounted-products.variant-details');

        Route::resource('discounted-products', DiscountedProductController::class)
            ->except(['show'])
            ->middleware('permission:discounted_products.access');

        Route::get('/customers', [CustomerController::class, 'index'])
            ->middleware('permission:customers.access')
            ->name('customers.index');

        Route::get('/stocks', [StockController::class, 'index'])
            ->middleware('permission:stocks.access')
            ->name('stocks.index');
        
        Route::resource('taxes', TaxController::class)
            ->except(['show']);

        Route::resource('roles', RoleController::class)
            ->except(['show'])
            ->middleware('permission:roles.access');

        Route::resource('users', UserManagementController::class)
            ->except(['show'])
            ->middleware('permission:users.access');
    });

require __DIR__.'/auth.php';