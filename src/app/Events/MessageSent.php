<?php

namespace App\Events;

use App\Library\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;    // \App\Library\Messageクラスのインスタンス

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // ここにpurchase_idをつけることで特定の商品のみチャットを受信できる
        return new PrivateChannel('channel.'.$this->message->receiver_id.'.'.$this->message->purchase_id);
    }

    public function broadcastAs()
    {
        return 'carmeri-chat';
    }
}
