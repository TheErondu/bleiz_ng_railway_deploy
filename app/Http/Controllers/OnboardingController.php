<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
     public function apply(Request $request){
        $user = Auth::user();
        if($user){
            return redirect()->route('home');
        }
        else{
            return  redirect()->route('login');
        }

     }
}
