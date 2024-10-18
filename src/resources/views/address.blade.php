@extends('layouts.base')
@section('title', '住所変更')
@section('modal')
  @include('components.modal')
@endsection
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div class="c-default-form" id="login">
    <h1 class="title">住所の変更</h1>
    <form class="form" action="{{ route('address.update', $item_id)}}" method="post">
      @csrf
      <label class="form-title">郵便番号</label>
      <input class="form-input" type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
      @error('postal_code')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">住所</label>
      <input class="form-input" type="text" name="address" value="{{ old('address', $user->address) }}">
      @error('address')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">建物名</label>
      <input class="form-input" type="text" name="building_name" value="{{ old('building_name', $user->building_name) }}">
      @error('building_name')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <button class="form-btn c-btn c-btn--red" type="submit">更新する</button>
    </form>
  </div>
@endsection