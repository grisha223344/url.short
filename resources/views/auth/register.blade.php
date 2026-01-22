@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <div class="login-page">
        <h2 class="login-page__title">Создайте аккаунт</h2>
        <p class="login-page__desc">Или <a href="{{ route('login.form') }}">войдите в существующий</a></p>
        <form class="form-auth" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-auth-field">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="form-auth-field">
                <label for="email">Email адрес</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="form-auth-field">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" value="{{ old('password') }}" required>
                @error('password')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="form-auth-field">
                <label for="password_confirmation">Подтвердите пароль</label>
                <input type="password" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" required>
                @error('password_confirmation')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="form-auth-btn">Зарегистрироваться</button>
        </form>
    </div>
@endsection
