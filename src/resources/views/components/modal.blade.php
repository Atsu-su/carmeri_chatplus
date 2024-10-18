@if (isset($message))
<div id="modal" class="c-modal">
  <div class="message">
    <div class="message-wrapper">
      @if ($message['status'] == 'success' || $message['status'] == 'info')
        <h1 class="title">{{ $message['title'] }}</h1>
        @foreach ($message['contents'] as $content)
          <p class="content">{{ $content }}</p>
        @endforeach
      @elseif ($message['status'] == 'error')
        <h1 class="title error">{{ $message['title'] }}</h1>
        @foreach ($message['contents'] as $content)
          <p class="content error">{{ $content }}</p>
        @endforeach
      @endif
      <a id="modal-btn" class="btn c-btn c-btn--modal-close">閉じる</a>
    </div>
  </div>
</div>
<script>
  const modal = document.getElementById('modal');
  const btn = document.getElementById('modal-btn');
  btn.addEventListener('click', () => {
    modal.classList.add('js-hidden');
  });
</script>
@endif