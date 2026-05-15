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
                'route_is' => 'admin.stocks.*',
                'icon' => 'bi bi-boxes',
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

];