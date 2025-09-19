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
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('onboarding/apply', [OnboardingController::class, 'apply'])->name('onboarding.apply');
Route::get('onboarding/register', [OnboardingController::class, 'apply'])->name('onboarding.register.email');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect(route('dashboard'));
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('onboarding/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('onboarding/register/email/verify', [OnboardingController::class, 'apply'])->name('onboarding.register.email.verify');
Route::get('onboarding/register/personal-details', [OnboardingController::class, 'apply'])->name('onboarding.register.personal-details');
Route::middleware(['auth', 'role:admin'])->prefix('admin')
    ->group(function () {
        Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'showAdminDashboard'])->name('admin.dashboard');
        Route::get('loans', [AdminLoanController::class, 'index'])->name('admin.loans.index');
        Route::get('loans/create', [AdminLoanController::class, 'create'])->name('admin.loans.create');
        Route::post('loans', [AdminLoanController::class, 'store'])->name('admin.loans.store');
        Route::get('loans/{loan}', [AdminLoanController::class, 'show'])->name('admin.loans.show');
        Route::get('loans/{loan}/edit', [AdminLoanController::class, 'edit'])->name('admin.loans.edit');
        Route::put('loans/{loan}', [AdminLoanController::class, 'update'])->name('admin.loans.update');
        Route::delete('loans/{loan}', [AdminLoanController::class, 'destroy'])->name('admin.loans.destroy');

        Route::resource('investors', InvestorController::class)->except(['destroy'])->names('admin.investors');
        Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'show'])->names('admin.transactions');
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update'])->names('admin.users');
        Route::resource('repayments', AdminRepaymentController::class)->names('admin.repayments');
        Route::resource('audit-logs', AdminRepaymentController::class)->names('admin.audit-logs');
        Route::resource('settings', SettingsController::class)->names('admin.settings');
        Route::resource('requests', AdminRepaymentController::class)->names('admin.requests');
        Route::post('/capital/inject', [CapitalPoolController::class, 'inject'])
            ->name('capital.inject');
    });

// CUSTOMER ROUTES
Route::middleware(['auth', 'role:customer', 'verified'])->prefix('customer')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'showCustomerDashboard'])->name('customer.dashboard');
    Route::get('loans', [CustomerLoanController::class, 'index'])->name('customer.loans.index');
    Route::get('loans/apply', [CustomerLoanController::class, 'create'])->name('customer.loans.create');
    Route::post('loans', [CustomerLoanController::class, 'store'])->name('customer.loans.store');
    Route::get('loans/{loan}', [CustomerLoanController::class, 'show'])->name('customer.loans.show');

    Route::get('loans/{loan}/repayments', [RepaymentScheduleController::class, 'index'])->name('customer.repayments.index');
    Route::get('loans/{loan}/repayments/pay', [RepaymentScheduleController::class, 'makePayment'])->name('customer.repayments.pay');
    Route::post('loans/{loan}/repayments', [RepaymentScheduleController::class, 'storePayment'])->name('customer.repayments.store');
});
