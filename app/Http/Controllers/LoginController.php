<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        if (Auth::check()){
            return redirect()->intended(route('user.report'));
        }
        $formFields = $request->only(['login', 'password']);

        if (Auth::attempt($formFields)){
            return redirect()->intended(route('user.report'));
        }

        redirect(route('user.login'))->withErrors([
            'login' => 'Не удалось авторизоваться'
        ]);
    }
}
