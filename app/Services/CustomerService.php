<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Str;

class CustomerService
{
    /**
     * Create a customer profile for the given user.
     *
     * @param  \App\Models\User  $user
     * @param  array             $data  // accepts keys: customer_code, business_name, address, phone_number
     * @return \App\Models\Customer
     */
    public function createCustomerForUser(User $user, array $data = []): Customer
    {
        $customer = Customer::create([
            'user_id'       => $user->id,
            'business_name' => $data['business_name'] ?? null,
            'address'       => $data['address'] ?? null,
            'phone_number'  => $data['phone_number'] ?? $user->phone_number,
        ]);

        return $customer;
    }


}
