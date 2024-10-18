@extends('layouts.base')
@section('title', 'メール確認')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="verify-email">
    <div class="message">
      <h2 class="message-title">メールアドレス確認</h2>
      <p>登録されたメールアドレスへメール確認のリンクを送付しました。</p>
      <p>メールをご確認ください。</p>
    </div>
    <form class="resend-form" method="POST" action="{{ route('verification.send') }}">
      @csrf
      <button class="c-btn c-btn--verify-resend" type="submit">確認メールを再送信</button>
    </form>
    @if (session('status') == 'verification-link-sent')
      <p class="resent">新しい確認メールを送信しました。</p>
    @endif
    <form class="logout-form" method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="c-btn c-btn--verify-logout logout-form-btn" type="submit">ログアウト</button>
    </form>
  </div>
@endsection