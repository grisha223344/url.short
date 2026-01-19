@extends('layouts.app')

@section('title', 'Главная - Сокращатель ссылок')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Сокращайте ссылки быстро и бесплатно
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">Создавайте короткие ссылки, отслеживайте статистику переходов и управляйте своими ссылками в одном месте.</p>
                @auth
                    <div class="mt-8">
                        <a href="{{ route('urls.create') }}"
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-plus mr-3"></i>Создать новую ссылку
                        </a>
                        <a href="{{ route('urls.index') }}"
                           class="ml-4 inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-link mr-3"></i>Мои ссылки
                        </a>
                    </div>
                @else
                    <div class="mt-8">
                        <a href="{{ route('register.form') }}"
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-user-plus mr-3"></i>Начать бесплатно
                        </a>
                        <a href="{{ route('login.form') }}"
                           class="ml-4 inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-sign-in-alt mr-3"></i>Войти
                        </a>
                    </div>
                @endauth
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <i class="fas fa-bolt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Мгновенное сокращение</h3>
                    <p class="text-gray-600">
                        Создавайте короткие ссылки за считанные секунды. Просто вставьте длинный URL и получите короткую ссылку.
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Подробная статистика</h3>
                    <p class="text-gray-600">
                        Отслеживайте количество кликов, уникальных посетителей и другую статистику по вашим ссылкам.
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Надежность и безопасность</h3>
                    <p class="text-gray-600">
                        Надежное хранение данных, возможность установки срока жизни ссылок и защита от злоупотреблений.
                    </p>
                </div>
            </div>

            <!-- Форма для гостей -->
            @guest
                <div class="mt-16 bg-gray-50 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Попробуйте прямо сейчас</h2>
                    <form id="guest-shorten-form" class="max-w-2xl mx-auto">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-grow">
                                <input type="url"
                                       name="original_url"
                                       id="guest-original-url"
                                       placeholder="Введите длинный URL (например, https://example.com)"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                            </div>
                            <div>
                                <button type="submit"
                                        class="w-full sm:w-auto px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Сократить
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="guest-result" class="mt-4 hidden">
                        <!-- Результат будет вставлен сюда через JavaScript -->
                    </div>
                </div>

                @push('scripts')
                    <script>
                        document.getElementById('guest-shorten-form').addEventListener('submit', function(e) {
                            e.preventDefault();

                            const form = e.target;
                            const formData = new FormData(form);
                            const resultDiv = document.getElementById('guest-result');

                            fetch('/api/shorten', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                                },
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.short_url) {
                                        resultDiv.classList.remove('hidden');
                                        resultDiv.innerHTML = `
                                            <div class="bg-green-50 p-4 rounded-md">
                                                <h3 class="font-medium text-green-800 mb-2">Ссылка успешно создана!</h3>
                                                <p class="mb-2">
                                                    <strong>Короткая ссылка:</strong>
                                                    <a href="${data.short_url}" target="_blank" class="text-indigo-600 hover:underline break-all">
                                                        ${data.short_url}
                                                    </a>
                                                </p>
                                                <p class="text-sm text-green-700">
                                                    Для получения статистики и управления ссылками зарегистрируйтесь.
                                                </p>
                                            </div>
                                        `;
                                    } else if (data.errors) {
                                        alert('Ошибка: ' + Object.values(data.errors).join(', '));
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Произошла ошибка при создании ссылки');
                                });
                        });
                    </script>
                @endpush
            @endguest
        </div>
    </div>
@endsection
