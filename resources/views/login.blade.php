<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Форма логина</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        {{-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript" language="JavaScript"></script> --}}
        <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
    </head>

    <body class="text-center">
        <main class="form-signin">
            <form action="{{ route('user.login') }}" method="post">
                @csrf
                <h1 class="h3 mb-3 fw-normal">Пожалуйста войдите</h1>
                <div class="form-floating">
                    <input type="text" name="login" class="form-control" id="login" placeholder="Your Login">
                    <label for="login">Логин</label>
                    @error('login')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Your Password">
                    <label for="password">Пароль</label>
                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Войти</button>
            </form>
        </main>
    </body>
</html>