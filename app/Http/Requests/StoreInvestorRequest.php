<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestorRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'phone_number' => 'nullable|string|regex:/^[\+]?[0-9\-\(\)\s]+$/|min:10|max:20',
            'capital' => 'required|numeric|min:50000|max:50000000', // 50k to 50M
            'roi_percentage' => 'required|numeric|min:5|max:25', // 5-25% ROI
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Name can only contain letters and spaces.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'capital.min' => 'Minimum investment amount is ₦50,000.',
            'capital.max' => 'Maximum investment amount is ₦50,000,000.',
            'roi_percentage.min' => 'Minimum ROI percentage is 5%.',
            'roi_percentage.max' => 'Maximum ROI percentage is 25%.',
        ];
    }
}
