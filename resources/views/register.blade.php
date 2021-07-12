<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        {{-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript" language="JavaScript"></script> --}}
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 mt-5 pt-3 pb-3 bg-secondary">
                    <div class="container">
                        <h3>Register</h3>
                        <hr>
                        <form class="" action="{{ route('user.register') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="name">Логин</label>
                                <input type="text" class="form-control" name="login" id="login" value="">
                                @error('login')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" class="form-control" name="name" id="name" value="">
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" name="password" id="password" value="">
                                @error('password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>