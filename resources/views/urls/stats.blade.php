@extends('layouts.app')

@section('title', 'Статистика ссылки')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Статистика ссылки</h1>
                <p class="text-gray-600 mt-2">
                    <a href="{{ $url->short_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 break-all">
                        {{ $url->short_url }}
                    </a>
                </p>
                <div class="mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $url->is_commercial ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $url->is_commercial ? 'Коммерческая ссылка' : 'Обычная ссылка' }}
                </span>
                    @if($url->expires_at)
                        @if($url->isExpired())
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Истекла
                    </span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Активна до {{ $url->expires_at->format('d.m.Y') }}
                    </span>
                        @endif
                    @endif
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('urls.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i>Назад
                </a>
                <a href="{{ $url->short_url }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-external-link-alt mr-2"></i>Перейти
                </a>
            </div>
        </div>
    </div>

    <!-- Основная статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Всего переходов</p>
                <p class="text-3xl font-semibold text-gray-900 mt-2">{{ $url->total_clicks }}</p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Уникальные посетители (14 дней)</p>
                <p class="text-3xl font-semibold text-gray-900 mt-2">{{ $uniqueVisitors }}</p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Создана</p>
                <p class="text-lg font-semibold text-gray-900 mt-2">{{ $url->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Оригинальный URL</p>
                <p class="text-xs text-gray-900 mt-2 truncate">{{ $url->original_url }}</p>
            </div>
        </div>
    </div>

    <!-- График переходов за последние 14 дней -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Переходы за последние 14 дней</h2>
        <div class="h-64">
            <canvas id="clicksChart"></canvas>
        </div>
    </div>

    <!-- История переходов -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">История переходов</h2>

            @if($clicksHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Время
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP-адрес
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User Agent
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Referer
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clicksHistory as $click)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $click->clicked_at->format('d.m.Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $click->ip_address }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="truncate max-w-xs">{{ $click->user_agent }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="truncate max-w-xs">
                                        @if($click->referer)
                                            <a href="{{ $click->referer }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $click->referer }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Прямой переход</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="mt-4">
                    {{ $clicksHistory->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-mouse-pointer text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">По этой ссылке пока не было переходов</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Получаем данные для графика
                const dates = Object.keys(@json($dateRange));
                const clicks = Object.values(@json($dateRange));

                // Создаем график
                const ctx = document.getElementById('clicksChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Переходы',
                            data: clicks,
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderColor: 'rgb(79, 70, 229)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
