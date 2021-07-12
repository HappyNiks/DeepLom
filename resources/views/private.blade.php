<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        
        {{-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript" language="JavaScript"></script> --}}
        <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
    </head>

    <body class="text-center">
        <main class="form-signin">
            <h1>Это приватная страница</h1>
            <p>Сюда попадают только аутнтифицированные пользователи</p>
            <form action="{{ route('user.logout') }}">
                <button class="w-100 btn btn-lg btn-primary" type="submit">Выйти</button>
            </form>
        </main>
    </body>
</html>