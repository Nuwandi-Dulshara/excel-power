<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductUnitController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\StockHistoryController;
use App\Http\Controllers\Admin\DamagedItemController;
use App\Http\Controllers\Admin\ReturnedItemController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DiscountedProductController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SettingController;
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
            return redirect()->route('admin.sales.index');
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

        Route::get('/stocks/create', [StockController::class, 'create'])
            ->middleware('permission:stocks.access')
            ->name('stocks.create');

        Route::post('/stocks', [StockController::class, 'store'])
            ->middleware('permission:stocks.access')
            ->name('stocks.store');

        Route::get('/stocks/reduce', [StockAdjustmentController::class, 'create'])
            ->middleware('permission:stocks.access')
            ->name('stocks.reduce');

        Route::post('/stocks/reduce/store', [StockAdjustmentController::class, 'store'])
            ->middleware('permission:stocks.access')
            ->name('stocks.reduce.store');

        Route::get('/stock-history', [StockHistoryController::class, 'index'])
            ->middleware('permission:stocks.access')
            ->name('stock-history.index');

        Route::resource('damaged-items', DamagedItemController::class)
            ->except(['show'])
            ->middleware('permission:stocks.access');

        Route::resource('returned-items', ReturnedItemController::class)
            ->except(['show'])
            ->middleware('permission:stocks.access');

        Route::get('/sales', [SaleController::class, 'index'])
            ->middleware('permission:sales.access')
            ->name('sales.index');

        Route::get('/sales/search-products', [SaleController::class, 'searchProducts'])
            ->middleware('permission:sales.access')
            ->name('sales.search-products');

        Route::post('/sales/save-draft', [SaleController::class, 'saveDraft'])
            ->middleware('permission:sales.access')
            ->name('sales.save-draft');

        Route::post('/sales/hold', [SaleController::class, 'hold'])
            ->middleware('permission:sales.access')
            ->name('sales.hold');

        Route::post('/sales/confirm', [SaleController::class, 'confirm'])
            ->middleware('permission:sales.access')
            ->name('sales.confirm');

        Route::get('/sales/held', [SaleController::class, 'held'])
            ->middleware('permission:sales.access')
            ->name('sales.held');

        Route::get('/sales/confirmed', [SaleController::class, 'confirmed'])
            ->middleware('permission:sales.access')
            ->name('sales.confirmed');

        Route::get('/sales/drafts', [SaleController::class, 'drafts'])
            ->middleware('permission:sales.access')
            ->name('sales.drafts');

        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])
            ->middleware('permission:sales.access')
            ->name('sales.edit');

        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])
            ->middleware('permission:sales.access')
            ->name('sales.cancel');

        Route::get('/sales/{sale}/print', [SaleController::class, 'print'])
            ->middleware('permission:sales.access')
            ->name('sales.print');
        
        Route::resource('taxes', TaxController::class)
            ->except(['show'])
            ->middleware('permission:taxes.access');

        Route::get('/settings', [SettingController::class, 'index'])
            ->middleware('permission:settings.access')
            ->name('settings.index');

        Route::put('/settings', [SettingController::class, 'update'])
            ->middleware('permission:settings.access')
            ->name('settings.update');

        Route::post('/settings/backup-database', [SettingController::class, 'backup'])
            ->middleware('permission:settings.access')
            ->name('settings.backup');

        Route::post('/settings/restore-database', [SettingController::class, 'restore'])
            ->middleware('permission:settings.access')
            ->name('settings.restore');

        Route::resource('roles', RoleController::class)
            ->except(['show'])
            ->middleware('permission:roles.access');

        Route::resource('users', UserManagementController::class)
            ->except(['show'])
            ->middleware('permission:users.access');
    });

require __DIR__.'/auth.php';
