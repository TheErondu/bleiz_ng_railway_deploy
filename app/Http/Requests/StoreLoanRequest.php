<?php

namespace App\Http\Requests;

use App\Services\CapitalPoolService;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        $capitalPool = app(CapitalPoolService::class)->getPool();
        $maxAmount = $capitalPool ? $capitalPool->available_amount : 0;

        return [
            'customer_id' => 'required|exists:customers,id',
            'principal' => [
                'required',
                'numeric',
                'min:10000', // Minimum 10k loan
                'max:' . $maxAmount, // Cannot exceed available capital
            ],
            'interest_rate' => 'required|numeric|min:5|max:50', // 5-50% annual rate
            'tenure_months' => 'required|integer|min:1|max:60', // 1-60 months
            'start_date' => 'required|date|after_or_equal:today|before:+1 year',
            'repayment_cycle' => 'required|in:monthly,weekly',
        ];
    }

    public function messages()
    {
        return [
            'principal.max' => 'Loan amount cannot exceed available capital pool balance.',
            'principal.min' => 'Minimum loan amount is ₦10,000.',
            'interest_rate.min' => 'Interest rate cannot be less than 5%.',
            'interest_rate.max' => 'Interest rate cannot exceed 50%.',
            'tenure_months.max' => 'Maximum loan tenure is 60 months.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'start_date.before' => 'Start date cannot be more than 1 year in the future.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $customer = \App\Models\Customer::find($this->customer_id);

            if ($customer) {
                // Check for existing active loans
                $activeLoans = $customer->loans()->where('status', 'ongoing')->count();
                if ($activeLoans >= 3) {
                    $validator->errors()->add('customer_id', 'Customer already has maximum number of active loans (3).');
                }

                // Check for overdue loans
                $overdueLoans = $customer->loans()->where('overdue_payment', '>', 0)->count();
                if ($overdueLoans > 0) {
                    $validator->errors()->add('customer_id', 'Customer has overdue payments on existing loans.');
                }
            }
        });
    }
}
