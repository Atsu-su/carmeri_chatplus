@extends('layouts.base')
@section('title', 'プロフィール入力')
@section('modal')
  @include('components.modal')
@endsection
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="profile" class="c-default-form">
    @error('is_changed')
      <p class="c-error-message">{{ $message }}</p>
    @enderror
    @if (!request()->headers->get('referer') === route('profile.edit'))
      <h1 class="title">プロフィール設定</h1>
    @else
      <h1 class="title">プロフィール編集</h1>
    @endif
    {{-- ここまで --}}
    <form class="form" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      {{-- c-default影響範囲外 ここから --}}

      {{--
          画像関係の流れ
          1. 登録済みの画像を表示する(done)
          2. 画像に変更が発生しない場合、コントローラでimageのアップデートは行わない
          3. 画像が変更された場合、画像を表示し、コントローラでimageのアップデートを行う
      --}}

      <div class="img-upload">
        <div id="background" class="c-profile-outer-frame img-upload-preview">
          {{-- 変更された画像を表示 --}}

          {{-- 登録されている画像を表示 --}}
          @if ($user->image && Storage::disk('public')->exists('profile_images/'.$user->image))
            <img id="preview" class="c-profile-inner-frame" src="{{ asset('storage/profile_images/'.$user->image) }}" alt="プロフィールの画像">
          @else
            <div id="no-image" class="c-profile-no-image">
              <p>NO</p>
              <p>IMAGE</p>
            </div>
          @endif
        </div>
        <label class="c-btn-img-select c-btn-img-select--profile" for="img-input">
          画像を選択する
        </label>
        <input class="img-upload-input" id="img-input" type="file" name="image" accept="image/*" style="display: none"/>
        <input id="is-changed" type="hidden" name="is_changed" value="false"/>
        <button class="img-upload-reset c-btn-img-reset c-btn-img-reset--profile" id="reset-btn" type="button">画像を削除</button>
      </div>
      {{-- ここまで c-default影響範囲外 --}}
      <p class="img-upload-file-name" id="file-name"></p>
      @error('image')
        <p id="img-error" class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title form-title-name">ユーザー名</label>
      <input class="form-input" type="text" name="name" value="{{ old('name', $user->name) }}">
      @error('name')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
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
      <button class="form-btn c-btn c-btn--red" type="submit">登録する</button>
    </form>
  </div>

  {{-- 画像プレビュー --}}
  <script>
    const imgInput = document.getElementById('img-input');
    const background = document.getElementById('background');
    const resetBtn = document.getElementById('reset-btn');
    const fileName = document.getElementById('file-name');
    const isChanged = document.getElementById('is-changed');

    function showPreview() {
      const file = event.target.files[0];

      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = function(e) {
          let preview = document.getElementById('preview');
          const noImage = document.getElementById('no-image');
          const imgError = document.getElementById('img-error');

          // 画像が変更されたことを示すフラグを立てる
          isChanged.value = 'true';

          if (!preview) {
            // 画像を表示するためのimg要素を生成
            preview = document.createElement('img');
            preview.id = 'preview';
            preview.className = 'c-profile-inner-frame';
            preview.alt = 'プロフィールの画像';

            // 背景要素に追加
            background.appendChild(preview);
          }

          preview.src = e.target.result;
          preview.style.display = 'block';  // 選択された画像を表示

          if (noImage) {
            noImage.style.display = 'none';
          }

          if (imgError !== null && imgError !== undefined) {
              imgError.style.display = 'none';
          }
        }

        reader.readAsDataURL(file);
        resetBtn.style.display = 'block';
      } else {
        if (preview) {
          preview.src = '';
          preview.style.display = 'none';
        }
        background.style.backgroundColor = '#D9D9D9';
      }
    }

    function resetPreview() {
      let preview = document.getElementById('preview');
      let noImage = document.getElementById('no-image');

      // 画像が変更されたことを示すフラグを立てる
      isChanged.value = 'true';

      if (preview) {
        preview.src = '';
        preview.style.display = 'none';   // 削除された画像のimg要素を非表示
        resetBtn.style.display = 'none';
        imgInput.value = ''; // ファイル入力をクリア（POSTされる値）
        fileName.textContent = ''; // ファイル名をクリア
      }

      if (!noImage) {
        noImage = document.createElement('div');
        noImage.id = 'no-image';
        noImage.className = 'c-profile-no-image';
        noImage.innerHTML = '<p>NO</p><p>IMAGE</p>';
        background.appendChild(noImage);
      } else {
        noImage.style.display = 'block';
      }
    }

    function switchResetBtn() {
      const preview = document.getElementById('preview');
      if (!preview) {
        resetBtn.style.display = 'none';
      }
    }

    document.addEventListener('DOMContentLoaded', switchResetBtn);
    imgInput.addEventListener('change', showPreview);
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