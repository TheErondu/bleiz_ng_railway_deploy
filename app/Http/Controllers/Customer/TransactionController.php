<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $users = User::all();
        $types = [
            'loan_disbursement' => 'Loan Disbursement',
            'repayment' => 'Loan Repayment',
            'investor_funding' => 'Investor Funding',
            'expense' => 'Expense'
        ];

        return view('admin.transactions.create', compact('users', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:loan_disbursement,repayment,investor_funding,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id'
        ]);

        $transaction = Transaction::create([
            'type' => $request->type,
            'reference_id' => Transaction::generateReferenceId(),
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'user_id' => $request->user_id
        ]);

        activity()->log("Created transaction: {$transaction->reference_id} - " . format_currency($transaction->amount));

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user');
        return view('admin.transactions.show', compact('transaction'));
    }
}
