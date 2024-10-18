@extends('layouts.base')
@section('title', 'ログイン')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div class="c-default-form" id="login">
    <h1 class="title">ログイン</h1>
    <form class="form" action="{{ route('login') }}" method="post">
      @csrf
      <label class="form-title">メールアドレス</label>
      <input class="form-input" type="text" name="email" value="{{ old('email') }}">
      @error('email')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">パスワード</label>
      <input class="form-input" type="password" name="password" value="{{ old('password') }}">
      @error('password')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <button class="form-btn c-btn c-btn--red" type="submit">ログインする</button>
    </form>
    <a class="login-link u-opacity-08" href="{{ route('register') }}">会員登録はこちら</a>
  </div>
@endsection