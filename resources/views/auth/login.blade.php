@extends('layouts.app')

@section('title', 'Вход')

@section('content')
    <div class="login-page">
        <h2 class="login-page__title">Войдите в свой аккаунт</h2>
        <p class="login-page__desc">Или <a href="{{ route('register.form') }}">создайте новый аккаунт</a></p>
        <form class="form-auth" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-auth-field">
                <label for="email">Email адрес</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <div class="form-auth-field">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <p>{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="form-auth-btn">Войти</button>
        </form>
    </div>
@endsection
