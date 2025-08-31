<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    RepaymentController as AdminRepaymentController,
    LoanController as AdminLoanController,
    InvestorController,
    TransactionController,
    AuditLogController,
    SettingsController,
    UserController as AdminUserController
};
use App\Http\Controllers\CapitalPoolController;
use App\Http\Controllers\Customer\{
    LoanController as CustomerLoanController,
    RepaymentScheduleController
};
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('onboarding/apply',[OnboardingController::class,'apply'])->name('onboarding.apply');

Route::middleware(['auth', 'role:admin'])->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('loans', [AdminLoanController::class, 'index'])->name('loans.index');
        Route::get('loans/create', [AdminLoanController::class, 'create'])->name('loans.create');
        Route::post('loans', [AdminLoanController::class, 'store'])->name('loans.store');
        Route::get('loans/{loan}', [AdminLoanController::class, 'show'])->name('loans.show');
        Route::get('loans/{loan}/edit', [AdminLoanController::class, 'edit'])->name('loans.edit');
        Route::put('loans/{loan}', [AdminLoanController::class, 'update'])->name('loans.update');
        Route::delete('loans/{loan}', [AdminLoanController::class, 'destroy'])->name('loans.destroy');

        Route::resource('investors', InvestorController::class)->except(['destroy']);
        Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
        Route::resource('repayments', AdminRepaymentController::class);
         Route::resource('audit', AdminRepaymentController::class);
              Route::resource('settings', SettingsController::class);
                Route::resource('requests', AdminRepaymentController::class);
    Route::post('/capital/inject', [CapitalPoolController::class, 'inject'])
    ->name('capital.inject');
    });

// CUSTOMER ROUTES
Route::middleware(['auth', 'role:customer'])->prefix('customer')->group(function () {
    Route::get('loans', [CustomerLoanController::class, 'index'])->name('customer.loans.index');
    Route::get('loans/apply', [CustomerLoanController::class, 'create'])->name('customer.loans.create');
    Route::post('loans', [CustomerLoanController::class, 'store'])->name('customer.loans.store');
    Route::get('loans/{loan}', [CustomerLoanController::class, 'show'])->name('customer.loans.show');

    Route::get('loans/{loan}/repayments', [RepaymentScheduleController::class, 'index'])->name('customer.repayments.index');
    Route::get('loans/{loan}/repayments/pay', [RepaymentScheduleController::class, 'makePayment'])->name('customer.repayments.pay');
    Route::post('loans/{loan}/repayments', [RepaymentScheduleController::class, 'storePayment'])->name('customer.repayments.store');
});
