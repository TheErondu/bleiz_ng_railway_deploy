<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepaymentRequest extends FormRequest
{
    public function authorize()
    {
        $loan = $this->route('loan');
        return $loan && $loan->customer->user_id === $this->user()->id;
    }

    public function rules()
    {
        $loan = $this->route('loan');
        $nextSchedule = $loan->schedules()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->first();

        $maxAmount = $nextSchedule ? $nextSchedule->amount_due - $nextSchedule->amount_paid : 0;

        return [
            'amount' => [
                'required',
                'numeric',
                'min:500', // Minimum payment
                'max:' . $maxAmount,
            ],
            'payment_method' => 'required|in:transfer,pos,cash',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date' => 'required|date|before_or_equal:today|after:' . $loan->start_date,
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'amount.min' => 'Minimum payment amount is ₦500.',
            'amount.max' => 'Payment amount cannot exceed the remaining due amount.',
            'payment_date.after' => 'Payment date cannot be before loan start date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }
}
