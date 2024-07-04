<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
    SdClient Auth
*/

Route::get('/login', function(){
	return redirect('/sdclient/redirect');
})->name('login');

Route::post('/logout', function(Request $request){
    Auth::logout();

	return redirect('/sdclient/logout');
})->name('logout');

Route::get('/sdclient/ready', function(){
	return redirect()->route('sites.index');
});

Route::get('/sdclient/error', function() {
    $error = session('sdclient.error');
    $error_description = session('sdclient.error_description');

    return 'There was an error signing in: ' . $error_description . ' (' . $error . ')<br><a href="/login">Try again</a>';
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
Route::get('/site/{site}/en', [SiteController::class, 'showEnglish'])->name('sites.show');
Route::get('/site/{site}', [SiteController::class, 'show'])->name('sites.show');
