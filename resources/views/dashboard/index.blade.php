@extends('layouts.app')

@section('title', 'Дашборд')

@section('content')
    <div class="dashboard-page">
        <h1 class="dashboard-page__title">Дашборд</h1>
        <p class="dashboard-page__desc">Обзор вашей активности и статистики</p>

        <div class="dashboard-stat">
            <div class="dashboard-stat-item">
                <div class="dashboard-stat-item__icon" style="background:rgb(224 231 255);color:rgb(79 70 229)">
                    <i class="fas fa-link"></i>
                </div>
                <div class="dashboard-stat-item__info">
                    <p class="dashboard-stat-item__title">Всего ссылок</p>
                    <p class="dashboard-stat-item__val">{{ $totalUrls }}</p>
                </div>
            </div>
            <div class="dashboard-stat-item">
                <div class="dashboard-stat-item__icon" style="background:rgb(220 252 231);color:rgb(22 163 74)">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="dashboard-stat-item__info">
                    <p class="dashboard-stat-item__title">Активных ссылок</p>
                    <p class="dashboard-stat-item__val">{{ $activeUrls }}</p>
                </div>
            </div>
            <div class="dashboard-stat-item">
                <div class="dashboard-stat-item__icon" style="background:rgb(219 234 254);color:rgb(37 99 235)">
                    <i class="fas fa-mouse-pointer text-blue-600 text-xl"></i>
                </div>
                <div class="dashboard-stat-item__info">
                    <p class="dashboard-stat-item__title">Всего переходов</p>
                    <p class="dashboard-stat-item__val">{{ $totalClicks }}</p>
                </div>
            </div>
        </div>

    </div>

    <div class="links-content">
        <div class="links-content-top">
            <div class="links-content-top__info">
                <h2 class="links-content__title">Мои ссылки</h2>
                <p class="links-content__desc">Все созданные вами короткие ссылки</p>
            </div>
            <div class="links-content-top__right">
                <a class="links-content-top__new" href="{{ route('urls.create') }}"><i class="fas fa-plus mr-2"></i>Новая ссылка</a>
            </div>
        </div>

        <div class="links-table-wrap">
            @if($urlList->count() > 0)
                <table class="links-table">
                    <thead class="links-table__head">
                        <tr>
                            <th class="links-table__head-th">Короткая ссылка</th>
                            <th class="links-table__head-th">Переходы</th>
                            <th class="links-table__head-th">Создана</th>
                            <th class="links-table__head-th">Срок жизни</th>
                            <th class="links-table__head-th">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="links-table__body">
                    @foreach($urlList as $url)
                        <tr>
                            <td class="links-table__body-td">
                                <div class="links-table-main-info">
                                    <div class="links-table-main-info__link">
                                        @if($url->is_commercial)
                                            <a href="{{ config('app.url') . '/' . $url->short_code }}" target="_blank" class="commerce-url">
                                                <i class="fas fa-ad mr-1"></i>{{ config('app.url') . '/' . $url->short_code }}
                                            </a>
                                            <span class="commerce-icon">
                                                <i class="fas fa-ad mr-1"></i>Реклама
                                            </span>
                                        @else
                                            <a href="{{ config('app.url') . '/' . $url->short_code }}" target="_blank" class="url">
                                                {{ config('app.url') . '/' . $url->short_code }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="links-table-main-info__original-url">{{ Str::limit($url->original_url, 60) }}</div>
                                    <div class="links-table-main-info__link-stat">
                                        <a href="{{ route('urls.stats', $url) }}">
                                            <i class="fas fa-chart-bar mr-1"></i>Статистика
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="links-table__body-td">
                                <div class="">{{ $url->clicks->count() }}</div>
                            </td>
                            <td class="links-table__body-td">
                                <span class="">{{ $url->created_at->format('d.m.Y H:i') }}</span>
                            </td>
                            <td class="links-table__body-td">
                                @if($url->expires_at)
                                    @if($url->isExpired())
                                        <span class="">
                                            Истекла {{ $url->expires_at->format('d.m.Y') }}
                                        </span>
                                    @else
                                        <span class="">
                                            До {{ $url->expires_at->format('d.m.Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="">Бессрочная</span>
                                @endif
                            </td>
                            <td class="links-table__body-td">
                                <div class="links-table__actions">
                                    <a href="{{ route('urls.stats', $url) }}" class="links-table__link-stat" title="Статистика">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <form action="{{ route('urls.destroy', $url) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="links-table__delete" title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="links-table-empty">
                    <p class="links-table-empty__title">У вас пока нет созданных ссылок</p>
                </div>
            @endif
        </div>
    </div>
@endsection
