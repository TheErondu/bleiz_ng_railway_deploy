<?php

namespace App\Http\Controllers;

use App\Models\DashboardStats;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = Auth::user();
        if ($user->hasRole('admin')) {

            return redirect(route('admin.dashboard'));
        } else {
            return redirect(route('customer.dashboard'));
        }
    }
    public function showAdminDashboard()
    {

        $stats = DashboardService::getDashboardStats();
        return view('admin.dashboard', compact('stats'));
    }
    public function showCustomerDashboard()
    {

        $stats = DashboardService::getDashboardStats();
        return view('customer.dashboard', compact('stats'));
    }
}
