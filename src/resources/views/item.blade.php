@extends('layouts.base')
@section('title', '商品詳細')
@section('modal')
  @include('components.modal')
@endsection
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="item">
    <div class="item-img">
      @if ($item->image && Storage::disk('public')->exists('item_images/'.$item->image))
        <img src="{{ asset('storage/item_images/'.$item->image) }}" width="600" height="600" alt="{{ $item->name }}の画像">
      @else
        <img class="c-no-image" src="{{ asset('img/'.'no_image.jpg') }}">
      @endif
    </div>
    <div class="item-detail">
      @if ($item->isOnSale())
        <h1 class="item-detail-title">{{ $item->name }}</h1>
      @else
        <h1 class="sold item-detail-title">{{ $item->name }}</h1>
      @endif
      <p class="item-detail-brand">{{ $item->brand ?? '' }}</p>
      <p class="item-detail-price">¥<span>{{ number_format($item->price) }}</span> (税込)</p>
      <div class="item-detail-icons">
        <div id="like-icon" class="item-detail-icons-icon item-detail-icons-like {{ $like ? 'filled' : '' }}" onclick="toggleLike({{ $item->id }}, '{{ route('like', $item->id) }}')">
          <span id="number-of-likes">{{ $item->likes_count }}</span>
        </div>
        <div class="item-detail-icons-icon item-detail-icons-comment">
          <span>{{ $item->comments_count }}</span>
        </div>
      </div>
      @if ($item->isOnSale())
        <a class="item-detail-btn c-btn c-btn--item" href="{{ route('purchase', $item->id) }}">購入手続きへ</a>
      @endif
      <h2 class="item-detail-title-about">商品説明</h2>
      <pre class="c-pre item-detail-about">{{ $item->description }}</pre>
      <h2 class="item-detail-title-general">商品の情報</h2>
      <table class="item-detail-general">
        <tr>
          <th>カテゴリ</th>
          <td>
            @foreach ($item->categoryItems as $categoryItem)
              <span class="c-label-category c-label-category--gray">{{ $categoryItem->category->category }}</span>
            @endforeach
          </td>
        </tr>
        <tr>
          <th>商品の状態</th>
          <td>{{ $item->condition->condition}}</td>
        </tr>
      </table>
      <div class="item-detail-comment">
        <h2 class="item-detail-comment-title">コメント({{ $item->comments_count}})</h2>

        {{-- ここからコメント --}}
        @foreach ($item->comments as $comment)
          <div class="item-detail-comment-commenter">
            <div class="item-detail-comment-commenter-frame">
              @if ($comment->user->image && Storage::disk('public')->exists('profile_images/'.$comment->user->image))
                <img src="{{ asset('storage/profile_images/'.$comment->user->image) }}" alt="プロフィールの画像">
              @else
                <p>NO</p>
                <p>IMAGE</p>
              @endif
            </div>
            <p class="item-detail-comment-commenter-user">{{ $comment->user->name }}</p>
          </div>
          <pre class="c-pre item-detail-comment-body">{{ $comment->comment }}</pre>
        @endforeach
        {{-- ここまでコメント --}}

        <h3 class="item-detail-comment-title-form">商品へのコメント</h3>
        @if (auth()->check())
        <div class="item-detail-comment-form">
          <form action="{{ route('comment.store', $item->id)}}" method="post">
            @csrf
            <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
            @error('comment')
              <p class="c-error-message">{{ $message }}</p>
            @enderror
            <button class="item-detail-comment-form-btn c-btn c-btn--item" type="submit">コメントを送信する</button>
          </form>
        </div>
        @else
        <p class="item-detail-comment-login">コメントをするには<a href="{{route('login')}}">ログイン</a>が必要です。</p>
        @endif
      </div>
    </div>
  </div>
  @if (auth()->check())
  <script>
    function toggleLike(itemId, url) {
      // 重複処理抑止用1
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const icon = document.getElementById('like-icon');

      if (icon.classList.contains('js-processing')) {
        console.log('処理中です');
        return;
      }

      icon.classList.add('js-processing');

      // いいねの状態を変更
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf}
      }).then(response =>  {
          if (!response.ok) {
            throw new Error('Network response was not OK');
          }
          return response.json();
      }).then(data => {
          const likeIcon = document.getElementById('like-icon');
          const likes = document.getElementById('number-of-likes');
          if (data.likeIt) {
            likes.textContent = parseInt(likes.textContent) + 1;  // いいねの数を増やす
            likeIcon.classList.add('filled'); // 星の色を黄色に変更
          } else {
            likes.textContent = parseInt(likes.textContent) - 1;  // いいねの数を減らす
            likeIcon.classList.remove('filled'); // 星の色を白色に変更
          }
      }).catch(error => {
          console.error('There has been a problem with your fetch operation:', error);
      }).finally(() => {
        // 重複処理抑止用2
        icon.classList.remove('js-processing');
      });
    }
  </script>
  @endif
@endsection