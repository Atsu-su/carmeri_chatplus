@extends('layouts.base')
@section('title', 'ユーザ登録')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div class="c-default-form" id="register">
    <h1 class="title">会員登録</h1>
    <form class="form" action="{{ route('register') }}" method="post">
      @csrf
      <label class="form-title">ユーザ名</label>
      <input class="form-input" type="text" name="name" value="{{ old('name') }}">
      @error('name')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">メールアドレス</label>
      <input class="form-input" type="text" name="email" value="{{ old('email') }}">
      @error('email')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">パスワード</label>
      <input class="form-input" type="password" name="password">
      @error('password')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">確認用パスワード</label>
      <input class="form-input" type="password" name="confirm_password">
      @error('confirm_password')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <button class="form-btn c-btn c-btn--red" type="submit">登録する</button>
    </form>
    <a class="login-link u-opacity-08" href="{{ route('login') }}">ログインはこちら</a>
  </div>
@endsection