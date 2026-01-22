@extends('layouts.app')

@section('title', 'Главная - Сокращатель ссылок')

@section('content')
    <div class="home-page">
        <h1 class="home-page__title">Сокращайте ссылки быстро и бесплатно</h1>
        <p class="home-page__desc">Создавайте короткие ссылки, отслеживайте статистику переходов и управляйте своими ссылками в одном месте.</p>
        <div class="advantages">
            <div class="advantages-item">
                <div class="advantages-item__icon">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
                <h3 class="advantages-item__title">Мгновенное сокращение</h3>
                <p class="advantages-item__desc">Создавайте короткие ссылки за считанные секунды. Просто вставьте длинный URL и получите короткую ссылку.</p>
            </div>
            <div class="advantages-item">
                <div class="advantages-item__icon">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <h3 class="advantages-item__title">Подробная статистика</h3>
                <p class="advantages-item__desc">Отслеживайте количество кликов, уникальных посетителей и другую статистику по вашим ссылкам.</p>
            </div>
            <div class="advantages-item">
                <div class="advantages-item__icon">
                    <i class="fas fa-shield-alt text-xl"></i>
                </div>
                <h3 class="advantages-item__title">Надежность и безопасность</h3>
                <p class="advantages-item__desc">Надежное хранение данных, возможность установки срока жизни ссылок и защита от злоупотреблений.</p>
            </div>
        </div>
    </div>
@endsection
