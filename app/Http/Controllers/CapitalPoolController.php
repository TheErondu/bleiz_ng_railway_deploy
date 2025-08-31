<?php

namespace App\Http\Controllers;

use App\Models\CapitalPool;
use App\Services\CapitalPoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CapitalPoolController extends Controller
{

     protected $capitalPoolService;

    public function __construct(CapitalPoolService $capitalPoolService)
    {
        $this->capitalPoolService = $capitalPoolService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

     /**
     * Handle fund injection.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inject(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $this->capitalPoolService->addFunds($request->amount);
            activity()->log('Injected funds: ' . format_currency($request->amount));
            return redirect()->route('dashboard')->with('success', 'Funds injected successfully.');
        } catch (\Exception $e) {
            Log::error('Fund injection failed: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Failed to inject funds.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CapitalPool $capitalPool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CapitalPool $capitalPool)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CapitalPool $capitalPool)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CapitalPool $capitalPool)
    {
        //
    }
}
