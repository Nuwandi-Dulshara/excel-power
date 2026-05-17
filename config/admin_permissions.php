<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Sidebar Permissions
    |--------------------------------------------------------------------------
    | To add a new sidebar/menu permission later:
    | 1. Add a new item here.
    | 2. Run: php artisan db:seed --class=RoleSeeder
    | 3. It will appear automatically in role create/edit checkboxes.
    */

    [
        'group' => 'Main Menu',
        'items' => [
            [
                'label' => 'Dashboard',
                'permission' => 'dashboard.access',
                'route' => 'admin.dashboard',
                'route_is' => 'admin.dashboard',
                'icon' => 'bi bi-grid-1x2-fill',
            ],
            [
                'label' => 'Parent Categories',
                'permission' => 'product_categories.access',
                'route' => 'admin.product-categories.index',
                'route_is' => 'admin.product-categories.*',
                'icon' => 'bi bi-tags-fill',
            ],
            [
                'label' => 'Product Units',
                'permission' => 'product_units.access',
                'route' => 'admin.product-units.index',
                'route_is' => 'admin.product-units.*',
                'icon' => 'bi bi-rulers',
            ],
            [
                'label' => 'Products',
                'permission' => 'products.access',
                'route' => 'admin.products.index',
                'route_is' => 'admin.products.*',
                'icon' => 'bi bi-box-seam-fill',
            ],
            [
                'label' => 'Discounted Products',
                'permission' => 'discounted_products.access',
                'route' => 'admin.discounted-products.index',
                'route_is' => 'admin.discounted-products.*',
                'icon' => 'bi bi-percent',
            ],
            [
                'label' => 'Sales / Billing',
                'permission' => 'sales.access',
                'route' => 'admin.sales.index',
                'route_is' => 'admin.sales.*',
                'icon' => 'bi bi-receipt',
            ],
        ],
    ],

    [
        'group' => 'People Management',
        'items' => [
            [
                'label' => 'Manage Suppliers',
                'permission' => 'suppliers.access',
                'route' => 'admin.suppliers.index',
                'route_is' => 'admin.suppliers.*',
                'icon' => 'bi bi-truck',
            ],
            [
                'label' => 'Manage Customers',
                'permission' => 'customers.access',
                'route' => 'admin.customers.index',
                'route_is' => 'admin.customers.*',
                'icon' => 'bi bi-people-fill',
            ],
        ],
    ],

    [
        'group' => 'Inventory',
        'items' => [
            [
                'label' => 'Manage Stocks',
                'permission' => 'stocks.access',
                'route' => 'admin.stocks.index',
                'route_is' => 'admin.stocks.index',
                'icon' => 'bi bi-boxes',
            ],
            [
                'label' => 'Add Stock',
                'permission' => 'stocks.access',
                'route' => 'admin.stocks.create',
                'route_is' => 'admin.stocks.create',
                'icon' => 'bi bi-plus-square',
            ],
            [
                'label' => 'Reduce Stock',
                'permission' => 'stocks.access',
                'route' => 'admin.stocks.reduce',
                'route_is' => 'admin.stocks.reduce',
                'icon' => 'bi bi-dash-square',
            ],
            [
                'label' => 'Stock History',
                'permission' => 'stocks.access',
                'route' => 'admin.stock-history.index',
                'route_is' => 'admin.stock-history.*',
                'icon' => 'bi bi-clock-history',
            ],
            [
                'label' => 'Damaged Items',
                'permission' => 'stocks.access',
                'route' => 'admin.damaged-items.index',
                'route_is' => 'admin.damaged-items.*',
                'icon' => 'bi bi-exclamation-octagon',
            ],
            [
                'label' => 'Returned Items',
                'permission' => 'stocks.access',
                'route' => 'admin.returned-items.index',
                'route_is' => 'admin.returned-items.*',
                'icon' => 'bi bi-arrow-counterclockwise',
            ],
        ],
    ],

    [
        'group' => 'System Management',
        'items' => [
            [
                'label' => 'Role Management',
                'permission' => 'roles.access',
                'route' => 'admin.roles.index',
                'route_is' => 'admin.roles.*',
                'icon' => 'bi bi-shield-lock-fill',
            ],
            [
                'label' => 'User Management',
                'permission' => 'users.access',
                'route' => 'admin.users.index',
                'route_is' => 'admin.users.*',
                'icon' => 'bi bi-person-gear',
            ],
        ],
    ],

    [
        'group' => 'Settings',
        'items' => [
            [
                'label' => 'Tax Settings',
                'permission' => 'taxes.access',
                'route' => 'admin.taxes.index',
                'route_is' => 'admin.taxes.*',
                'icon' => 'bi bi-receipt-cutoff',
            ],
            [
                'label' => 'Store Settings',
                'permission' => 'settings.access',
                'route' => 'admin.settings.index',
                'route_is' => 'admin.settings.*',
                'icon' => 'bi bi-gear-fill',
            ],
        ],
    ],

];
