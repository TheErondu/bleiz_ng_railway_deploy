<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalkController;
use App\Jobs\AddDatatoDB;
use App\Jobs\SendNotification;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('talks/create', [TalkController::class, 'create'])->name('talks.create');
    Route::post('talks', [TalkController::class, 'store'])->name('talks.store');

    
});

Route::get('/test', function() {
    dd(env('MAIL_HOST'), env(('APP_ENV')));
});

Route::get('/jobs', function() {

    // /**
    //  * dispatch SendNotification and AddDataToDB Job
    //  */
    // dispatch(new SendNotification, new AddDatatoDB);

    Bus::chain([
        new AddDatatoDB,
        new SendNotification
    ])->dispatch();

    echo "Your jobs are being dispatching....";
});

require __DIR__.'/auth.php';
