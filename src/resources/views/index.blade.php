@extends('layouts.base')
@section('title', 'Carmeri')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div class="c-items" id="index">
    <div class="titles">
      <h2 class="title title-recommend js-active-title" data-tab="first-tab">おすすめ</h2>
      <h2 class="title title-mylist" data-tab="second-tab">マイリスト</h2>
    </div>

    {{-- おすすめ --}}
    <div class="tab first-tab">
      @foreach ($items as $item)
        <a class="c-item" href="{{ route('item.show', $item->id) }}">
          @if ($item->image && Storage::disk('public')->exists('item_images/'.$item->image))
            <img src="{{ asset('storage/item_images/'.$item->image) }}" width="290" height="281" alt="{{ $item->name }}の画像">
          @else
            <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}" width="290" height="281" alt="商品の画像がありません">
          @endif
          @if ($item->isOnSale())
            <p>{{ $item->name }}</p>
          @else
            <p class="sold">{{ $item->name }}</p>
          @endif
        </a>
      @endforeach
    </div>

    {{-- マイリスト --}}
    <div class="tab second-tab js-hidden">
      @if (auth()->check())
        @foreach ($likedItems as $like)
          <a class="c-item" href="{{ route('item.show', $like->item_id) }}">
            @if ($like->item->image && Storage::disk('public')->exists('item_images/'.$like->item->image))
              <img src="{{ asset('storage/item_images/'.$like->item->image) }}" width="290" height="281" alt="【商品名】の画像">
            @else
              <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}" width="290" height="281" alt="商品の画像がありません">
            @endif
            @if ($like->item->isOnSale())
              <p>{{ $like->item->name }}</p>
            @else
              <p class="sold">{{ $like->item->name }}</p>
            @endif
          </a>
        @endforeach
      @else
        <p class="second-tab-login"><a href="{{ route('login') }}">ログイン</a>後に表示されます</p>
      @endif
    </div>
  </div>
  <script>
    const titles = document.querySelectorAll('.title');
    const tabs = document.querySelectorAll('.tab');

    titles.forEach(title => {
      title.addEventListener('click', (e) => {
        /* ・クリックされたタイトルにacitiveクラスが付いていない場合
             1. クリックされたタイトルにactiveクラスを付与
             2. クリックされなかったタイトルからactiveクラスを削除
             3. クリックされたタイトルに紐づくタブを表示
             4. クリックされなかったタイトルに紐づくタブを非表示

           ・クリックされたタイトルにactiveクラスが付いている場合、
             何もしない
        */
        if (! e.target.classList.contains('js-active-title')) {
          e.target.classList.add('js-active-title');

          titles.forEach(title => {
            if (e.target !== title) {
              title.classList.remove('js-active-title');
            }
          })

          tabs.forEach(tab => {
            if (tab.classList.contains(e.target.dataset.tab)) {
              tab.classList.remove('js-hidden');
            } else {
              tab.classList.add('js-hidden');
            }
          });
        }
      })
    })
  </script>
@endsection