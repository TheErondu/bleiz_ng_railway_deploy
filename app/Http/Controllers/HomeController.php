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
            $stats = DashboardService::getDashboardStats();
            return view('admin.dashboard', compact('stats'));
        } else {
            return view('customer.dashboard');
        }
    }
}
