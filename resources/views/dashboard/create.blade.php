@extends('layouts.app')

@section('title', 'Создать короткую ссылку')

@section('content')
    <div class="page-crate">
        <h2 class="page-crate__title">Создание короткой ссылки</h2>
        <p class="page-crate__desc">Создайте короткую ссылку для любого URL. Вы можете настроить её параметры.</p>
        <form action="{{ route('urls.store') }}" class="create-link-form" method="POST">
            @csrf
            <div class="form-field">
                <label for="original_url" class="form-field__label">Оригинальный URL *</label>
                <input type="url" name="original_url" id="original_url" placeholder="https://example.com/very-long-url" value="{{ old('original_url') }}" required>
                @error('original_url')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="form-field">
                <label for="expires_in" class="form-field__label">Срок жизни ссылки (дни)</label>
                <input type="number" name="expires_in" id="expires_in" min="1" max="365" placeholder="30 (или оставьте пустым)" value="{{ old('expires_in') }}">
                @error('expires_in')
                <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="form-field-checkbox">
                <input type="checkbox" name="is_commercial" id="is_commercial" value="1" {{ old('is_commercial') ? 'checked' : '' }}>
                <label for="is_commercial" class="form-field__label">Коммерческая ссылка (с показом рекламы перед переходом)</label>
            </div>
            <div class="form-commercial-block">
                <h3 class="form-commercial-block__title"><i class="fas fa-money-bill-wave mr-2"></i>Настройки платных показов</h3>
                <p class="form-commercial-block__desc">Используется только для коммерческих ссылок</p>
                <div class="form-field">
                    <label for="cost_per_view" class="form-field__label">Стоимость одного уникального показа (руб.)</label>
                    <input type="number" name="cost_per_view" id="cost_per_view" min="0.00" max="1000" step="0.01" placeholder="1.00" value="{{ old('cost_per_view', 0.00) }}">
                </div>
                <div class="form-field">
                    <label for="budget" class="form-field__label">Общий бюджет (руб.)</label>
                    <input type="number" name="budget" id="budget" min="0" max="1000000" step="0.01" placeholder="100.00" value="{{ old('budget', 0.00) }}">
                </div>
            </div>
            <button type="submit" class="create-link-form-btn">Создать ссылку</button>
        </form>
    </div>
@endsection
