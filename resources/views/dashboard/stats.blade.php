@extends('layouts.app')

@section('title', 'Статистика ссылки')

@section('content')
    <div class="stats-page">
        <h1 class="stats-page__title">Статистика ссылки</h1>
        <p class="stats-page__desc">
            <a href="{{ $urlLink }}" target="_blank" class="url">
                {{ $urlLink }}
            </a>
        </p>

        <div class="dashboard-stat">
            <div class="dashboard-stat-item">
                <div class="dashboard-stat-item__icon" style="background:rgb(224 231 255);color:rgb(79 70 229)">
                    <i class="fas fa-link"></i>
                </div>
                <div class="dashboard-stat-item__info">
                    <p class="dashboard-stat-item__title">Всего переходов</p>
                    <p class="dashboard-stat-item__val">{{ $clicksHistory->count() }}</p>
                </div>
            </div>
            <div class="dashboard-stat-item">
                <div class="dashboard-stat-item__icon" style="background:rgb(220 252 231);color:rgb(22 163 74)">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="dashboard-stat-item__info">
                    <p class="dashboard-stat-item__title">Уникальные посетители (14 дней)</p>
                    <p class="dashboard-stat-item__val">{{ $clicksLast14Days }}</p>
                </div>
            </div>
            @if($url->is_commercial)
                <div class="dashboard-stat-item">x
                    <div class="dashboard-stat-item__info">
                        <p class="dashboard-stat-item__title">Бюджет</p>
                        <p class="dashboard-stat-item__val">{{ $url->budget }}</p>
                    </div>
                </div>
                <div class="dashboard-stat-item">x
                    <div class="dashboard-stat-item__info">
                        <p class="dashboard-stat-item__title">Цена перехода</p>
                        <p class="dashboard-stat-item__val">{{ $url->cost_per_view }}</p>
                    </div>
                </div>
                <div class="dashboard-stat-item">x
                    <div class="dashboard-stat-item__info">
                        <p class="dashboard-stat-item__title">Потрачено</p>
                        <p class="dashboard-stat-item__val">{{ $url->budget_spent }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="links-table-wrap">
            @if($clicksHistory->count() > 0)
                <table class="links-table">
                    <thead class="links-table__head">
                    <tr>
                        <th class="links-table__head-th">Время</th>
                        <th class="links-table__head-th">IP-адрес</th>
                        <th class="links-table__head-th">User Agent</th>
                    </tr>
                    </thead>
                    <tbody class="links-table__body">
                    @foreach($clicksHistory as $click)
                        <tr>
                            <td class="links-table__body-td">
                                <span class="">{{ $click->created_at->format('d.m.Y H:i:s') }}</span>
                            </td>
                            <td class="links-table__body-td">
                                <span class="">{{ $click->ip_address }}</span>
                            </td>
                            <td class="links-table__body-td">
                                <span class="">{{ $click->user_agent }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="links-table-empty">
                    <p class="links-table-empty__title">По этой ссылке пока не было переходов</p>
                </div>
            @endif
        </div>

    </div>
@endsection
