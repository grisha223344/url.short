@extends('layouts.app')

@section('title', 'Дашборд')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Дашборд</h1>
        <p class="text-gray-600 mt-2">Обзор вашей активности и статистики</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <i class="fas fa-link text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Всего ссылок</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalUrls }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Активных ссылок</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeUrls }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-mouse-pointer text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Всего переходов</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalClicks }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Быстрые действия</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('urls.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>Создать ссылку
            </a>
            <a href="{{ route('urls.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-list mr-2"></i>Все ссылки
            </a>
        </div>
    </div>

    <!-- Последние ссылки -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Последние ссылки</h2>
                <a href="{{ route('urls.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Показать все
                </a>
            </div>

            @if($recentUrls->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Короткая ссылка</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Оригинальный URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Переходы</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Создана</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentUrls as $url)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ $url->short_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $url->short_url }}
                                        </a>
                                        @if($url->is_commercial)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Реклама
                                </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <a href="{{ route('urls.stats', $url) }}" class="hover:text-indigo-600">
                                            Статистика
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 truncate max-w-xs">{{ $url->original_url }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $url->total_clicks }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $url->created_at->format('d.m.Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-link text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">У вас пока нет созданных ссылок</p>
                    <a href="{{ route('urls.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-900 font-medium">
                        Создайте свою первую ссылку
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
