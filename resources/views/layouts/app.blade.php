<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Сокращатель ссылок')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js для графиков -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-50">
<!-- Навигация -->
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-link mr-2"></i>ShortLink
                    </a>
                </div>

                @auth
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}"
                           class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Дашборд
                        </a>
                        <a href="{{ route('urls.index') }}"
                           class="{{ request()->routeIs('urls.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-link mr-2"></i>Мои ссылки
                        </a>
                        <a href="{{ route('urls.create') }}"
                           class="{{ request()->routeIs('urls.create') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-plus mr-2"></i>Создать ссылку
                        </a>
                    </div>
                @endauth
            </div>

            <div class="flex items-center">
                @auth
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <div class="relative ml-3">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-700">
                                    Привет, {{ Auth::user()->name }}!
                                </span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Выйти
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-4">
                        <a href="{{ route('login.form') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>Войти
                        </a>
                        <a href="{{ route('register.form') }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-user-plus mr-2"></i>Регистрация
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Основной контент -->
<main class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('new_url'))
                    <div class="mt-2 p-3 bg-green-50 rounded">
                        <p class="font-semibold">Новая короткая ссылка:</p>
                        <p class="mt-1">
                            <a href="{{ session('new_url.short_url') }}" target="_blank" class="text-indigo-600 hover:underline">
                                {{ session('new_url.short_url') }}
                            </a>
                        </p>
                        <p class="mt-1 text-sm">
                            Статистика:
                            <a href="{{ session('new_url.stats_url') }}" class="text-indigo-600 hover:underline">
                                {{ session('new_url.stats_url') }}
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        @endif

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

<!-- Футер -->
<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} ShortLink. Все права защищены.</p>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
