<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepaymentScheduleController extends Controller
{
    public function index($loan)
    { /* show repayment schedule */
    }

    public function makePayment($loan)
    { /* show payment form */
    }

    public function storePayment(Request $request, $loan)
    { /* handle payment submission */
    }
}
