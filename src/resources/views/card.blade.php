@extends('layouts.base')
@section('title', 'カード情報入力')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="card" class="c-default-form">
    @if (session('flash_alert'))
      <p class="alert alert-danger">{{ session('flash_alert') }}</p>
    @elseif(session('status'))
      <p class="alert alert-success">{{ session('status') }}</p>
    @endif
    <h1 class="title">Stripe決済</h1>
    <form action="{{ route('payment.store') }}" method="POST">
      @csrf
        <label class="form-title">カード番号</label>
        <div id="card-number" class="form-input"></div>
        <label class="form-title">有効期限</label>
        <div id="card-expiry" class="form-input"></div>
        <label class="form-title">セキュリティコード</label>
        <div id="card-cvc" class="form-input"></div>

      <p id="card-errors" class="error-message"></p>
      <button class="form-btn c-btn c-btn--red">支払い</button>
    </form>
  </div>

  {{-- Stripe --}}
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    /* 基本設定*/
    const elementStyles = {
      base: {
          fontFamily: "sans-serif",
          lineHeight: "59px",
          height: "59px",
          fontSize: "30px",
          paddingTop: "5px",
          paddingLeft: "10px",
          "::placeholder": {
              color: "#aaa"
          },
          ":-webkit-autofill": {
              color: "#e39f48"
          }
      },
      invalid: {
          color: "#FF5555"
      }
    };

    const stripe_public_key = "{{ config('stripe.stripe_public_key') }}"
    const stripe = Stripe(stripe_public_key);
    const elements = stripe.elements();

    var cardNumber = elements.create('cardNumber', {
      style: elementStyles
    });
    cardNumber.mount('#card-number');
    cardNumber.on('change', function(event) {
      var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });

    var cardExpiry = elements.create('cardExpiry',{
      style: elementStyles
    });
    cardExpiry.mount('#card-expiry');
    cardExpiry.on('change', function(event) {
      var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });

    var cardCvc = elements.create('cardCvc', {
      style: elementStyles
    });
    cardCvc.mount('#card-cvc');
    cardCvc.on('change', function(event) {
      var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });

    var form = document.getElementById('card-form');
    form.addEventListener('submit', function(event) {
      event.preventDefault();
      var errorElement = document.getElementById('card-errors');
      if (event.error) {
        errorElement.textContent = event.error.message;
      } else {
        errorElement.textContent = '';
      }

      stripe.createToken(cardNumber).then(function(result) {
        if (result.error) {
          errorElement.textContent = result.error.message;
        } else {
          stripeTokenHandler(result.token);
        }
      });
    });

    function stripeTokenHandler(token) {
      var form = document.getElementById('card-form');
      var hiddenInput = document.createElement('input');
      hiddenInput.setAttribute('type', 'hidden');
      hiddenInput.setAttribute('name', 'stripeToken');
      hiddenInput.setAttribute('value', token.id);
      form.appendChild(hiddenInput);
      form.submit();
    }
  </script>
@endsection