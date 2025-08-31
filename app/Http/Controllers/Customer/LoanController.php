<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    { /* list customer's loans */
    }

    public function show($loan)
    { /* show loan details */
    }

    public function create()
    { /* show loan application form */
    }

    public function store(Request $request)
    { /* submit loan application */
    }
}
