<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        return view('dashboards.admin');
    }

    public function cashier(): View
    {
        return view('dashboards.cashier');
    }

    public function developer(): View
    {
        return view('dashboards.developer');
    }
}