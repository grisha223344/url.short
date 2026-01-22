<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Сокращатель ссылок')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>
<div class="top-line">
    <div class="container">
        <div class="top-line_nav">
            <a href="{{ route('home') }}" class="top-line__logo">
                <i class="fas fa-link mr-2"></i>ShortLink
            </a>
            @auth
                <div class="navigation">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>Дашборд
                    </a>
                    <a href="{{ route('urls.create') }}">
                        <i class="fas fa-plus mr-2"></i>Создать ссылку
                    </a>
                </div>
            @endauth
        </div>
        <div class="top-line__auth">
            @auth
                <div class="auth-user-info">
                    <span class="tauth-user-info__name">
                        Привет, {{ auth()->user()->name }}!
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="auth-user-info__logout-form">
                        @csrf
                        <button type="submit" class="auth-user-info__logout-btn">
                            <i class="fas fa-sign-out-alt mr-2"></i>Выйти
                        </button>
                    </form>
                </div>
            @else
                <div class="auth-enter">
                    <a href="{{ route('login.form') }}" class="auth-enter__btn-login">
                        <i class="fas fa-sign-in-alt mr-2"></i>Войти
                    </a>
                    <a href="{{ route('register.form') }}" class="auth-enter__btn-register">
                        <i class="fas fa-user-plus mr-2"></i>Регистрация
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<main class="content">
    <div class="container">
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<footer class="footer">
    <p class="footer__text">&copy; {{ date('Y') }} ShortLink. Все права защищены.</p>
</footer>

@stack('scripts')
</body>
</html>
