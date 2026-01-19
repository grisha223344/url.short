@extends('layouts.app')

@section('title', 'Создать короткую ссылку')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Создание короткой ссылки</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Создайте короткую ссылку для любого URL. Вы можете настроить её параметры.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('urls.store') }}" method="POST">
                @csrf

                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <!-- Оригинальный URL -->
                            <div class="col-span-6">
                                <label for="original_url" class="block text-sm font-medium text-gray-700">
                                    Оригинальный URL *
                                </label>
                                <input type="url" name="original_url" id="original_url" required
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="https://example.com/very-long-url"
                                       value="{{ old('original_url') }}">
                                @error('original_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Пользовательский код -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="custom_code" class="block text-sm font-medium text-gray-700">
                                    Пользовательский код (необязательно)
                                </label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    {{ config('app.url') }}/
                                </span>
                                    <input type="text" name="custom_code" id="custom_code"
                                           class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300"
                                           placeholder="my-page"
                                           value="{{ old('custom_code') }}">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Только буквы, цифры, дефисы и подчеркивания. Оставьте пустым для автоматической генерации.
                                </p>
                                @error('custom_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Срок жизни -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="expires_in" class="block text-sm font-medium text-gray-700">
                                    Срок жизни ссылки (дни)
                                </label>
                                <input type="number" name="expires_in" id="expires_in" min="1" max="365"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="30 (или оставьте пустым)"
                                       value="{{ old('expires_in') }}">
                                <p class="mt-1 text-xs text-gray-500">
                                    Оставьте пустым, чтобы ссылка не имела срока жизни
                                </p>
                                @error('expires_in')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Коммерческая ссылка -->
                            <div class="col-span-6">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_commercial" id="is_commercial" value="1"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        {{ old('is_commercial') ? 'checked' : '' }}>
                                    <label for="is_commercial" class="ml-2 block text-sm text-gray-900">
                                        Коммерческая ссылка (с показом рекламы перед переходом)
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    При включении этой опции пользователи будут видеть рекламу в течение 5 секунд перед переходом
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Секция платных показов (показывается только если выбрана коммерческая ссылка) -->
                    <div id="paidAdsSection" class="col-span-6 bg-gray-50 p-4 rounded-md mt-4" style="display: none;">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-money-bill-wave mr-2"></i>Настройки платных показов
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Стоимость показа -->
                            <div>
                                <label for="cost_per_view" class="block text-sm font-medium text-gray-700">
                                    Стоимость одного уникального показа (руб.)
                                </label>
                                <input type="number"
                                       name="cost_per_view"
                                       id="cost_per_view"
                                       min="0.01"
                                       max="1000"
                                       step="0.01"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="1.00"
                                       value="{{ old('cost_per_view', 1.00) }}">
                                <p class="mt-1 text-xs text-gray-500">
                                    Стоимость списывается только за уникальные показы
                                </p>
                            </div>

                            <!-- Бюджет -->
                            <div>
                                <label for="budget" class="block text-sm font-medium text-gray-700">
                                    Общий бюджет (руб.)
                                </label>
                                <input type="number"
                                       name="budget"
                                       id="budget"
                                       min="0"
                                       max="1000000"
                                       step="0.01"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="100.00"
                                       value="{{ old('budget', 100.00) }}">
                                <p class="mt-1 text-xs text-gray-500">
                                    При исчерпании бюджета реклама показываться не будет
                                </p>
                            </div>

                            <!-- Максимальное количество показов в день -->
                            <div>
                                <label for="max_daily_views" class="block text-sm font-medium text-gray-700">
                                    Максимальное количество показов в день (необязательно)
                                </label>
                                <input type="number"
                                       name="max_daily_views"
                                       id="max_daily_views"
                                       min="0"
                                       max="100000"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Не ограничено"
                                       value="{{ old('max_daily_views') }}">
                            </div>

                            <!-- Даты кампании -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Даты кампании (необязательно)
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="campaign_start_date" class="block text-xs text-gray-500 mb-1">
                                            Дата начала
                                        </label>
                                        <input type="date"
                                               name="campaign_start_date"
                                               id="campaign_start_date"
                                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               value="{{ old('campaign_start_date') }}">
                                    </div>
                                    <div>
                                        <label for="campaign_end_date" class="block text-xs text-gray-500 mb-1">
                                            Дата окончания
                                        </label>
                                        <input type="date"
                                               name="campaign_end_date"
                                               id="campaign_end_date"
                                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               value="{{ old('campaign_end_date') }}">
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Если не указаны, кампания будет активна бессрочно
                                </p>
                            </div>

                            <!-- Расчетная информация -->
                            <div class="md:col-span-2 mt-4 p-3 bg-blue-50 rounded-md">
                                <h4 class="font-medium text-blue-800 mb-2">
                                    <i class="fas fa-calculator mr-2"></i>Расчетная информация
                                </h4>
                                <div id="budgetInfo" class="text-sm text-blue-700">
                                    <p>Рассчитанная информация появится после ввода данных</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @push('scripts')
                        <script>
                            // Показ/скрытие секции платных показов
                            document.getElementById('is_commercial').addEventListener('change', function() {
                                const paidAdsSection = document.getElementById('paidAdsSection');
                                if (this.checked) {
                                    paidAdsSection.style.display = 'block';
                                    updateBudgetInfo();
                                } else {
                                    paidAdsSection.style.display = 'none';
                                }
                            });

                            // Инициализация при загрузке
                            document.addEventListener('DOMContentLoaded', function() {
                                const isCommercial = document.getElementById('is_commercial');
                                if (isCommercial.checked) {
                                    document.getElementById('paidAdsSection').style.display = 'block';
                                    updateBudgetInfo();
                                }
                            });

                            // Обновление информации о бюджете
                            function updateBudgetInfo() {
                                const costPerView = parseFloat(document.getElementById('cost_per_view').value) || 0;
                                const budget = parseFloat(document.getElementById('budget').value) || 0;

                                let html = '';

                                if (costPerView > 0 && budget > 0) {
                                    const estimatedViews = Math.floor(budget / costPerView);
                                    const costPerViewFormatted = costPerView.toFixed(2);
                                    const budgetFormatted = budget.toFixed(2);

                                    html = `
                                        <p>Стоимость показа: <strong>${costPerViewFormatted} руб.</strong></p>
                                        <p>Бюджет: <strong>${budgetFormatted} руб.</strong></p>
                                        <p class="mt-2 font-semibold">
                                            Ориентировочное количество показов: <span class="text-green-600">${estimatedViews}</span>
                                        </p>
                                        <p class="text-xs mt-1">
                                            * Расчет приблизительный, исходя из уникальных показов
                                        </p>
                                    `;
                                } else {
                                    html = '<p>Введите стоимость показа и бюджет для расчета</p>';
                                }

                                document.getElementById('budgetInfo').innerHTML = html;
                            }

                            // Слушатели изменения полей
                            document.getElementById('cost_per_view').addEventListener('input', updateBudgetInfo);
                            document.getElementById('budget').addEventListener('input', updateBudgetInfo);
                        </script>
                    @endpush

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('urls.index') }}"
                           class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Отмена
                        </a>
                        <button type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Создать ссылку
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
