<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class InvestorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of investors.
     */
    public function index()
    {
        $investors = Investor::with(['user', 'withdrawals'])
            ->withCount('withdrawals')
            ->latest()
            ->paginate(15);

        $totalCapital = Investor::sum('capital');
        $totalROIAccrued = Investor::sum('roi_accrued');
        $totalWithdrawn = Withdrawal::sum('amount');

        return view('admin.investors.index', compact(
            'investors',
            'totalCapital',
            'totalROIAccrued',
            'totalWithdrawn'
        ));
    }

    /**
     * Show the form for creating a new investor.
     */
    public function create()
    {
        return view('admin.investors.create');
    }

    /**
     * Store a newly created investor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'capital' => 'required|numeric|min:10000', // Minimum 10k investment
            'roi_percentage' => 'required|numeric|min:0|max:50',
        ]);

        DB::transaction(function () use ($request) {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
            ]);

            // Assign investor role
            $user->assignRole('investor');

            // Create investor profile
            Investor::create([
                'user_id' => $user->id,
                'amount_provided' => $request->capital,
                'date_contributed' => now(),
                'status' => 'active',
                'capital' => $request->capital,
                'roi_percentage' => $request->roi_percentage,
                'roi_accrued' => 0,
                'roi_withdrawn' => 0,
            ]);

            activity()->log("Created investor: {$user->name} with capital " . format_currency($request->capital));
        });

        return redirect()->route('admin.investors.index')
            ->with('success', 'Investor created successfully.');
    }

    /**
     * Display the specified investor.
     */
    public function show(Investor $investor)
    {
        $investor->load(['user', 'withdrawals' => function ($query) {
            $query->latest();
        }]);

        // Get monthly withdrawal history
        $monthlyWithdrawals = $investor->withdrawals()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Calculate performance metrics
        $totalInvested = $investor->capital;
        $totalWithdrawn = $investor->withdrawals->sum('amount');
        $availableROI = $investor->roi_accrued - $totalWithdrawn;
        $effectiveROI = $totalInvested > 0 ? ($investor->roi_accrued / $totalInvested) * 100 : 0;

        return view('admin.investors.show', compact(
            'investor',
            'monthlyWithdrawals',
            'totalInvested',
            'totalWithdrawn',
            'availableROI',
            'effectiveROI'
        ));
    }

    /**
     * Show the form for editing the specified investor.
     */
    public function edit(Investor $investor)
    {
        $investor->load('user');
        return view('admin.investors.edit', compact('investor'));
    }

    /**
     * Update the specified investor.
     */
    public function update(Request $request, Investor $investor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($investor->user_id)
            ],
            'phone_number' => 'nullable|string|max:20',
            'capital' => 'required|numeric|min:0',
            'roi_percentage' => 'required|numeric|min:0|max:50',
            'status' => 'required|in:active,withdrawn',
        ]);

        DB::transaction(function () use ($request, $investor) {
            // Update user details
            $investor->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            // Update investor details
            $oldCapital = $investor->capital;
            $investor->update([
                'capital' => $request->capital,
                'roi_percentage' => $request->roi_percentage,
                'status' => $request->status,
            ]);

            if ($oldCapital != $request->capital) {
                activity()->log("Updated investor {$investor->user->name} capital from "
                    . format_currency($oldCapital) . " to " . format_currency($request->capital));
            }
        });

        return redirect()->route('admin.investors.show', $investor)
            ->with('success', 'Investor updated successfully.');
    }

    /**
     * Process withdrawal request.
     */
    public function processWithdrawal(Request $request, Investor $investor)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000', // Minimum 1k withdrawal
            'note' => 'nullable|string|max:500',
        ]);

        $availableROI = $investor->roi_accrued - $investor->totalWithdrawn();

        if ($request->amount > $availableROI) {
            return redirect()->back()
                ->with('error', 'Withdrawal amount exceeds available ROI balance.');
        }

        $withdrawal = $investor->withdrawals()->create([
            'amount' => $request->amount,
            'status' => 'approved',
            'note' => $request->note,
            'approved_at' => now(),
        ]);

        activity()->log("Processed withdrawal for {$investor->user->name}: " . format_currency($request->amount));

        return redirect()->route('admin.investors.show', $investor)
            ->with('success', 'Withdrawal processed successfully.');
    }

    /**
     * Get investor statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total_investors' => Investor::count(),
            'active_investors' => Investor::where('status', 'active')->count(),
            'total_capital' => Investor::sum('capital'),
            'total_roi_accrued' => Investor::sum('roi_accrued'),
            'total_withdrawn' => Withdrawal::sum('amount'),
            'average_roi_rate' => Investor::avg('roi_percentage'),
        ];

        return response()->json($stats);
    }
}
