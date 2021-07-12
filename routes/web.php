<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()){
        return redirect(route('user.report'));
    }
    return view('login');
});
Route::name('user.')->group(function(){
    Route::get('/login', function(){
        if (Auth::check()){
            return redirect(route('report'));
        }
        return view('login');
    })->name('login');
 
    Route::get('/logout', function(){
        Auth::logout();
        return redirect('login');
    })->name('logout');
    
    Route::get('/register', function(){
        if (Auth::check()){
            return redirect(route('user.report'));
        }
        return view('register');
    })->name('register');

    Route::get('/report', function(){
        if (Auth::check()){
            return view('report');
        }
        return view('login');
    })->name('report');

    Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'save']);
    Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');
    Route::post('/report', [\App\Http\Controllers\RequestController::class, 'report'])->name('report');
    Route::post('/', [\App\Http\Controllers\RequestController::class, 'filter_list'])->name('filter');
    Route::get('/report', [\App\Http\Controllers\RequestController::class, 'get_list'])->middleware('auth')->name('report');
});
