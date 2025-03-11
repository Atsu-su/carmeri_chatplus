@extends('layouts.base')
@section('title', 'プロフィール')
@section('modal')
  @include('components.modal')
@endsection
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="mypage">
    <div class="user">
      <div class="user-info">
        <div class="c-profile-outer-frame user-info-icon">
          @if ($user->image && Storage::disk('public')->exists('profile_images/'.$user->image))
            <img class="c-profile-inner-frame" src="{{ asset('storage/profile_images/'.$user->image) }}" alt="プロフィールの画像">
          @else
            <div class="c-profile-no-image">
              <p>NO</p>
              <p>IMAGE</p>
            </div>
          @endif
        </div>
        <div>
          <p class="user-info-name">{{ $user->name }}</p>
          <div class="user-info-stars">
            @if ($user->evaluations > 0)
              @for ($i = 1; $i <= 5; $i++)
                <span class="user-info-stars-star{{$i}}{{ $i <= $user->rating ? ' filled' : '' }}"></span>
              @endfor
            @else
              <p class="user-info-stars-zero">評価はまだありません</p>
            @endif
          </div>
        </div>
      </div>
      <a class="c-btn c-btn--profile-edit" href="{{ route('profile.edit')}}">プロフィールを編集</a>
    </div>
    <div class="c-items">
      <div class="titles">
        <h2 class="title title-recommend js-active-title" data-tab="first-tab">出品した商品</h2>
        <h2 class="title title-mylist" data-tab="second-tab">購入した商品</h2>
        @php $result = $sellingItems->sum(function ($item) {return $item->chats->count();}) @endphp
        <h2 class="title title-processing" data-tab="third-tab">取引中の出品商品
          @if ($result > 0)
            <span class="new-message-icon"><span>{{ $result < 100 ? $result : '99+' }}</span></span>
          @endif
        </h2>
        @php $result = $purchasingItems->sum(function ($item) {return $item->chats->count();}) @endphp
        <h2 class="title title-processing" data-tab="fourth-tab">取引中の購入商品
          @if ($result > 0)
            <span class="new-message-icon"><span>{{ $result < 100 ? $result : '99+' }}</span></span>
          @endif
        </h2>
      </div>
      <div class="tab first-tab">
        @if ($listedItems->isEmpty())
          <p class="no-listed-item">出品された商品はありません</p>
        @else
          @foreach ($listedItems as $item)
            <a class="c-item" href="{{ route('item.show', $item->id) }}">
              @if ($item->image && Storage::disk('public')->exists('item_images/'.$item->image))
                <img src="{{ asset('storage/item_images/'.$item->image) }}" width="290" height="281" alt="{{ $item->name }}の画像">
              @else
                <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}" width="290" height="281" alt="商品の画像がありません">
              @endif
              @if ($item->on_sale)
                <p>{{ $item->name }}</p>
              @else
                <p class="sold">{{ $item->name }}</p>
              @endif
            </a>
          @endforeach
        @endif
      </div>
      <div class="tab second-tab js-hidden">
        @if ($purchasedItems->isEmpty())
          <p class="no-purchased-item">購入された商品はありません</p>
        @else
          @foreach ($purchasedItems as $purchase)
            <a class="c-item" href="{{ route('item.show', $purchase->item->id) }}">
              @if ($purchase->item->image && Storage::disk('public')->exists('item_images/'.$purchase->item->image))
                <img src="{{ asset('storage/item_images/'.$purchase->item->image) }}" width="290" height="281" alt="{{ $purchase->name }}の画像">
              @else
                <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}" width="290" height="281" alt="商品の画像がありません">
              @endif
              <p class="sold">{{ $purchase->item->name }}</p>
            </a>
          @endforeach
        @endif
      </div>
      <div class="tab third-tab js-hidden">
        @if ($sellingItems->isEmpty())
          <p class="no-purchased-item">出品している商品のうち、取引中の商品はありません</p>
        @else
          @foreach ($sellingItems as $index => $purchase)
            <a class="c-item" href="{{ route('chat', $purchase->id) }}">
              <div class="image-container">
                @if ($purchase->item->image && Storage::disk('public')->exists('item_images/'.$purchase->item->image))
                  <img src="{{ asset('storage/item_images/'.$purchase->item->image) }}" width="290" height="281" alt="{{ $purchase->name }}の画像">
                @else
                  <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}" width="290" height="281" alt="商品の画像がありません">
                @endif
                @php $result = $purchase->chats->count() @endphp
                @if ($result > 0)
                  <span class="new-message-icon2">{{ $result < 100 ? $result : '99+' }}</span>
                @endif
              </div>
              <p>{{ $purchase->item->name }}</p>
            </a>
          @endforeach
        @endif
      </div>
      <div class="tab fourth-tab js-hidden">
        @if ($purchasedItems->isEmpty())
          <p class="no-purchased-item">購入した商品のうち、取引中の商品はありません</p>
        @else
          @foreach ($purchasingItems as $index => $purchase)
            <a class="c-item" href="{{ route('chat', $purchase->id) }}">
              <div class="image-container">
                @if ($purchase->item->image && Storage::disk('public')->exists('item_images/'.$purchase->item->image))
                  <img src="{{ asset('storage/item_images/'.$purchase->item->image) }}" width="290" height="281" alt="{{ $purchase->name }}の画像">
                @else
                  <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}" width="290" height="281" alt="商品の画像がありません">
                @endif
                @php $result = $purchase->chats->count() @endphp
                @if ($result > 0)
                  <span class="new-message-icon2">{{ $result < 100 ? $result : '99+' }}</span>
                @endif
              </div>
              <p>{{ $purchase->item->name }}</p>
            </a>
          @endforeach
        @endif
      </div>
    </div>
  </div>

  {{-- タブ切り替え --}}
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