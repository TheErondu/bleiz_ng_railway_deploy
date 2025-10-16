<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    RepaymentController as AdminRepaymentController,
    LoanController as AdminLoanController,
    LoanRequestController as AdminLoanRequestController,
    InvestorController,
    TransactionController as AdminTransactionController,
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
use App\Http\Controllers\WithdrawalsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

Auth::routes();
// Route::get('/test-mail', function () {
//     try {
//         Mail::raw('Hello Erondu! This is a test email from your Laravel Mailgun setup.', function ($message) {
//             $message->to('erone007@gmail.com')
//                     ->subject('Mailgun Test - Laravel');
//         });

//         return '✅ Test email sent successfully to erone007@gmail.com';
//     } catch (\Exception $e) {
//         return '❌ Mail failed: ' . $e->getMessage();
//     }
// });
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('onboarding/apply', [OnboardingController::class, 'apply'])->name('onboarding.apply');
Route::get('onboarding/register', [OnboardingController::class, 'apply'])->name('onboarding.register.email');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect(route('dashboard'))->with('success', 'Email verified successfully!');
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

        // Loans
         Route::prefix('loans')->name('admin.loans.')->group(function () {
        Route::get('/', [AdminLoanController::class, 'index'])->name('index');
        Route::get('/{loan}', [AdminLoanController::class, 'show'])->name('show');

        // Approve loan
        Route::put('/{loan}/approve', [AdminLoanController::class, 'approve'])->name('approve');

        // Reject loan
        Route::put('/{loan}/reject', [AdminLoanController::class, 'reject'])->name('reject');
    });

        // Investors
        Route::resource('investors', InvestorController::class)->except(['destroy'])->names('admin.investors');

        // Transactions
        Route::resource('transactions', AdminTransactionController::class)->only(['index', 'create', 'store', 'show'])->names('admin.transactions');

        // Users
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update'])->names('admin.users');

        // Repayments
        Route::resource('repayments', AdminRepaymentController::class)->names('admin.repayments');

        // Audit Logs - Fix: Use correct controller
        Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show'])->names('admin.audit-logs');

        // Settings
        Route::resource('settings', SettingsController::class)->names('admin.settings');

        // Loan Requests - Fix: Use correct controller
        Route::resource('requests', AdminLoanRequestController::class)->names('admin.requests');

        // Capital Pool
        Route::post('/capital/inject', [CapitalPoolController::class, 'inject'])->name('admin.capital.inject');

        // Withdrawals management
        Route::resource('withdrawals', WithdrawalsController::class)->names('admin.withdrawals');
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
