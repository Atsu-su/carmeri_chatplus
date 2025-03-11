<?php

namespace App\Library;

class Message
{
    public $chat_id;
    public $receiver_id;    // 受信者のユーザID
    public $purchase_id;    // 購入ID
    public $username;       // 送信者の名前
    public $message;        // 送信メッセージ
    public $datetime;       // 送信時間（サーバ）
    public $image;          // 送信画像
}
