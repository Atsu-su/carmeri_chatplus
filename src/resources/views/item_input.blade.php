@extends('layouts.base')
@section('title', '商品の出品')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="item-input">
    <h1 class="title">商品の出品</h1>
    <form class="form" action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="img-upload">
        <h2 class="img-upload-title">商品の画像</h2>
        <div class="img-upload-container">
          <div id="background" class="img-upload-background">
            <img id="preview" src="" width="100" height="100">
          </div>
          <label id="label" class="img-upload-img-select c-btn-img-select c-btn-img-select--profile" for="img-input">
            画像を選択する
          </label>
          <input class="img-upload-input" id="img-input" type="file" name="image" accept="image/*"/>
        </div>
        @error('image')
          <p id="img-error" class="c-error-message">{{ $message }}</p>
        @enderror
        <p class="img-upload-file-name" id="file-name"></p>
        <button class="img-upload-reset c-btn-img-reset c-btn-img-reset--profile" id="reset-btn" type="button">画像を削除</button>
      </div>
      <h2 class="form-title">商品の詳細</h2>
      <label class="form-name form-name-category">カテゴリ</label>
      <div class="form-category">
        @foreach ($categories as $category)
          <input type="checkbox" id="{{ $loop->iteration }}" value="{{ $category->id }}" name="category_id[]" {{in_array($category->id, old('category_id', [])) ? 'checked' : '' }}>
          <label for="{{ $loop->iteration }}">{{ $category->category }}</label>
        @endforeach
      </div>
      @error('category_id')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-name form-name-condition">商品の状態</label>
      <div class="form-condition-wrapper">
        <select class="form-condition" name="condition_id">
          <option value="">選択してください</option>
          @foreach ($conditions as $condition)
            <option value="{{ $condition->id }}" {{ old('condition') == $condition->id ? 'selected' : '' }}>{{ $condition->condition}}</option>
          @endforeach
        </select>
        @error('condition')
          <p class="c-error-message">{{ $message }}</p>
        @enderror
      </div>
      <h2 class="form-title form-title-detail">商品名と説明</h2>
      <label class="form-name">商品名</label>
      <input class="form-input" type="text" name="name" value="{{ old('name') }}">
      @error('name')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-name form-name-brand">ブランド</label>
      <input class="form-input" type="text" name="brand" value="{{ old('brand') }}">
      @error('brand')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-name form-name-description">商品の説明</label>
      <textarea class="form-textarea" name="description"> {{ old('description') }}</textarea>
      @error('description')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-name form-name-price">販売価格</label>
      <div class="form-price-wrapper">
        <input class="form-input form-input-price" type="text" name="price" value="{{ old('price') }}">
      </div>
      @error('price')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <button class="form-btn c-btn c-btn--red" type="submit">登録する</button>
    </form>
  </div>

  {{-- 画像プレビュー --}}
  <script>
    const imgInput = document.getElementById('img-input');
    const preview = document.getElementById('preview');
    const background = document.querySelector('.img-upload-background');
    const resetBtn = document.getElementById('reset-btn');
    const fileName = document.getElementById('file-name');
    const label = document.getElementById('label');
    const imgError = document.getElementById('img-error');

    imgInput.addEventListener('change', function(e) {
      const file = e.target.files[0];

      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        // ロード後の処理
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
          background.style.display = 'block';
          resetBtn.style.display = 'block';
          label.style.display = 'none';
          fileName.textContent = `ファイル名：${file.name}`;
          if (imgError !== null && imgError !== undefined) {
              imgError.style.display = 'none';
          }
        }

        reader.readAsDataURL(file);
      }
    });

    function resetPreview() {
      preview.src = '';
      preview.style.display = 'none';
      background.style.backgroundColor = '#D9D9D9';
      resetBtn.style.display = 'none';
      imgInput.value = ''; // ファイル入力をクリア（POSTされる値）
      fileName.textContent = ''; // ファイル名をクリア
      label.style.display = 'grid';
      background.style.display = 'none';
    }

    resetBtn.addEventListener('click', resetPreview);
  </script>

  {{-- 画像選択後にファイル名を表示 --}}
  <script>
    document.getElementById('img-input').addEventListener('change', function() {
      var fileName = this.files[0] ? this.files[0].name : '';
      document.getElementById('file-name').textContent = `ファイル：${fileName}`;
    });
  </script>
@endsection