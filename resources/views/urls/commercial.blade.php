<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рекламное объявление - ShortLink</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .progress-bar {
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #4f46e5;
            width: 0%;
            transition: width 1s linear;
        }

        .ad-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .ad-image {
            max-height: 400px;
            object-fit: contain;
        }

        .countdown {
            font-size: 3rem;
            font-weight: bold;
            color: #4f46e5;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen">
<div class="min-h-screen flex flex-col items-center justify-center p-4">
    <!-- Шапка -->
    <div class="w-full max-w-4xl mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-link text-2xl text-indigo-600 mr-2"></i>
                <span class="text-xl font-bold text-gray-800">ShortLink</span>
            </div>
            <div class="text-sm text-gray-600">
                Рекламное объявление
            </div>
        </div>

        @if($isPaid)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-green-800">Платный показ зарегистрирован</p>
                        <p class="text-sm text-green-700 mt-1">
                            Списано: <span class="font-semibold">{{ number_format($cost, 2) }} руб.</span>
                            | Остаток бюджета: <span class="font-semibold">{{ number_format($shortUrl->remaining_budget, 2) }} руб.</span>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-blue-800">Рекламный показ (бесплатный)</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Этот показ уже был засчитан ранее для вашего устройства.
                            Спасибо за поддержку нашего сервиса!
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Прогресс-бар -->
        <div class="mt-4 progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="ad-container bg-white rounded-2xl shadow-xl overflow-hidden w-full">
        <!-- Заголовок -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-center">
            <h1 class="text-2xl font-bold text-white mb-2">
                <i class="fas fa-ad mr-2"></i>Рекламное объявление
            </h1>
            <p class="text-indigo-100">
                Поддержите наш сервис, просмотрев рекламу
            </p>
        </div>

        <!-- Изображение -->
        <div class="p-6 md:p-8">
            <div class="text-center mb-6">
                <div class="countdown mb-4" id="countdown">5</div>
                <p class="text-gray-600 mb-2">Секунд до перехода</p>
            </div>

            @if($randomImage)
                <div class="mb-8">
                    <div class="bg-gray-100 rounded-xl p-2 mb-4">
                        <img src="{{ $randomImage }}"
                             alt="Рекламное объявление"
                             class="ad-image w-full rounded-lg shadow-md mx-auto">
                    </div>
                    <p class="text-center text-gray-500 text-sm">
                        Изображение предоставлено рекламодателем
                    </p>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-image text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Рекламное изображение не найдено</p>
                </div>
            @endif

            <!-- Информация о ссылке -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-indigo-500 mt-1 mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-800 mb-1">Вы будете перенаправлены на:</p>
                        <p class="text-sm text-gray-600 break-all">{{ $shortUrl->original_url }}</p>
                    </div>
                </div>
            </div>

            <!-- Добавить информацию о бюджете в информационный блок -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Бюджет кампании</p>
                        <p class="text-lg font-bold text-gray-800">
                            {{ number_format($shortUrl->budget, 2) }} руб.
                        </p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Потрачено</p>
                        <p class="text-lg font-bold text-orange-600">
                            {{ number_format($shortUrl->budget_spent, 2) }} руб.
                        </p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Осталось показов</p>
                        <p class="text-lg font-bold text-green-600">
                            {{ $shortUrl->estimated_remaining_views ?? '∞' }}
                        </p>
                    </div>
                </div>

                <!-- Прогресс-бар бюджета -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Использование бюджета</span>
                        <span>{{ number_format($shortUrl->budget_percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-yellow-500 h-2 rounded-full"
                             style="width: {{ min($shortUrl->budget_percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Кнопка пропуска -->
            <div class="text-center">
                <button id="skipButton"
                        disabled
                        class="inline-flex items-center px-6 py-3 bg-gray-100 border border-transparent rounded-lg font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-forward mr-2"></i>
                    <span id="skipText">Пропустить рекламу (через <span id="skipCountdown">5</span> сек.)</span>
                </button>

                <p class="mt-4 text-sm text-gray-500">
                    Поддержка рекламодателей помогает нам поддерживать бесплатный сервис
                </p>
            </div>
        </div>

        <!-- Футер -->
        <div class="bg-gray-50 p-4 text-center border-t border-gray-200">
            <p class="text-xs text-gray-500">
                <i class="fas fa-shield-alt mr-1"></i>
                Ваши данные защищены. Мы не храним информацию о просмотренных объявлениях.
            </p>
        </div>
    </div>

    <!-- Социальные ссылки -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-600 mb-4">
            Нравится наш сервис? Расскажите друзьям!
        </p>
        <div class="flex justify-center space-x-4">
            <a href="#" class="text-gray-400 hover:text-blue-600">
                <i class="fab fa-vk text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-blue-400">
                <i class="fab fa-telegram text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-green-500">
                <i class="fab fa-whatsapp text-xl"></i>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        const skipButton = document.getElementById('skipButton');
        const skipCountdownElement = document.getElementById('skipCountdown');
        const progressFill = document.getElementById('progressFill');
        const skipText = document.getElementById('skipText');

        // Начальное состояние прогресс-бара
        let progressWidth = 0;
        const progressInterval = 100; // 100ms интервалы
        const totalIntervals = 5000 / progressInterval; // 5 секунд

        // Обновляем прогресс-бар
        const progressIntervalId = setInterval(() => {
            progressWidth += (100 / totalIntervals);
            progressFill.style.width = progressWidth + '%';
        }, progressInterval);

        // Таймер обратного отсчета
        const countdownInterval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            skipCountdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdownInterval);
                clearInterval(progressIntervalId);
                window.location.href = "{{ route('commercial.redirect', $code) }}";
            }

            // Включаем кнопку пропуска после 3 секунд
            if (seconds <= 2) {
                skipButton.disabled = false;
                skipButton.classList.remove('bg-gray-100', 'text-gray-700');
                skipButton.classList.add('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                skipText.innerHTML = '<i class="fas fa-forward mr-2"></i>Перейти сейчас';
            }
        }, 1000);

        // Обработчик кнопки пропуска
        skipButton.addEventListener('click', function() {
            if (!this.disabled) {
                clearInterval(countdownInterval);
                clearInterval(progressIntervalId);
                window.location.href = "{{ route('commercial.redirect', $code) }}";
            }
        });
    });
</script>
</body>
</html>
