<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function save(Request $request){
        if (Auth::check()){
            return redirect(route('user.report'));
        }
        $validateFields = $request->validate([
            'login' => 'required',
            'name' => 'required',
            'password' => 'required',
        ]);

        if (User::where('login', $validateFields['login'])->exists()){
            redirect(route('user.register'))->withErrors([
                'login' => 'Такой пользователь уже зарегистрирован'
            ]);
        }

        $user = User::create($validateFields);
        if ($user){
            Auth::login($user);
            return redirect(route('user.report'));
        }
        return redirect(route('user.login'))->withErrors([
            'formError' => 'Произошла ошибка при сохранении пользователя'
        ]);
    }
}
