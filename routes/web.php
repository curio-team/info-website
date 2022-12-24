<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Auth;

/*
    AmoClient Auth
*/

Route::get('/login', function(){
	return redirect('/amoclient/redirect');
})->name('login');

Route::post('/logout', function(){
    Auth::logout();

	return redirect('/amoclient/logout');
})->name('logout');

Route::get('/amoclient/ready', function(){
	return redirect()->route('sites.index');
});

/*
    App
*/

Route::get('/', [SiteController::class, 'random'])->name('home');
Route::get('/en', [SiteController::class, 'randomEnglish'])->name('home.english');

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        Route::resource('beheer', SiteController::class, [
            'names' => 'sites'
        ])->parameters([
            'beheer' => 'site'
        ])->except([
            'show'
        ]);
    });

Route::middleware('auth.test')->group(function () {
    Route::get('/test', [SiteController::class, 'testShow'])->name('sites.test.show');
    Route::post('/test', [SiteController::class, 'testSubmit'])->name('sites.test.submit');
    Route::get('/test/submitted', [SiteController::class, 'testSubmitted'])->name('sites.test.submitted');
});

// Overrides default Resource route
Route::get('/site/{site}', [SiteController::class, 'show'])->name('sites.show');
