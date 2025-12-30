<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;

class AdminDashboardController extends Controller
{

public function index()
{
    $ordersCount = CustomerOrder::where('status', '!=', 'annulee')->count();

    return view('admin.dashboard', [
        'ordersCount' => $ordersCount,
    ]);
}

}