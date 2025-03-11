<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Library\Message;
use App\Models\Chat;
use App\Models\Purchase;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function index($purchase_id)
    {
        $user = auth()->user();
        $notSeller = false; // true: 購入者（出品者ではない）, false: 出品者
        $notBuyer = false;  // true: 出品者（購入者ではない）, false: 購入者

        // このユーザが出品者かどうかを判定
        try {
            Purchase::query()
                ->whereHas('item', function ($query) use ($user) {
                    $query->where('seller_id', $user->id);
                })
                ->where('id', $purchase_id)
                ->firstOrFail();
        } catch (Exception $e) {
            // 出品者ではない（購入者）
            $notSeller = true;
        }

        // このユーザが購入者かどうか判定
        try {
            Purchase::query()
                ->where('buyer_id', $user->id)
                ->where('id', $purchase_id)
                ->firstOrFail();
        } catch (Exception $e) {
            // 購入者ではない（出品者）
            $notBuyer = true;
        }

        // このユーザが出品者または購入者ではない場合はアクセスを拒否
        if ($notSeller && $notBuyer) {
            abort(403);
        }

        // チャット情報を取得
        $chats = Chat::query()
            ->with('user:id,name,image')
            ->where('purchase_id', $purchase_id)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // チャットを既読に変更する
        $chats->map(function ($chat) use ($user) {
            // 送信者のチャットかつ未読の場合は既読に変更
            if ($chat->sender_id != $user->id && !$chat->is_read) {
                $chat->update(['is_read' => true]);
            }
        });

        // 取引相手の情報を取得
        $purchase = null;

        if (!$notSeller && $notBuyer) {
            // 出品者の場合は購入者の情報を取得
            $purchase = Purchase::query()
                ->with(['item:id,name,price,image', 'user:id,name,image'])
                ->where('id', $purchase_id)
                ->select('id', 'item_id', 'buyer_id')
                ->first();
        } else {
            // 購入者の場合は出品者の情報を取得
            $purchase = Purchase::query()
                ->with(['item:id,seller_id,name,price,image', 'item.user:id,name,image'])
                ->where('id', $purchase_id)
                ->select('id', 'item_id')
                ->first();
        }

        // 現在取引中の出品商品
        $sellingItems = Purchase::query()
            ->with('item:id,name')
            ->whereHas('item', function ($query) use ($user) {
                $query->where('seller_id', $user->id);
            })
            ->where('status', Purchase::PROCESSING)
            ->where('id', '!=', $purchase_id)
            ->select('id', 'item_id')
            ->get();

        // 商品名が長い場合は省略する
        $sellingItems->map(function ($purchase) {
            $purchase->item->name = mb_strlen($purchase->item->name) > 7
                ? mb_substr($purchase->item->name, 0, 7) . '...'
                : $purchase->item->name;
            return $purchase;});

        // 現在取引中の購入商品
        $purchasingItems = Purchase::query()
            ->with('item:id,name')
            ->where('buyer_id', $user->id)
            ->where('status', Purchase::PROCESSING)
            ->where('id', '!=', $purchase_id)
            ->select('id', 'item_id')
            ->get();

        // 商品名が長い場合は省略する
        $purchasingItems->map(function ($purchase) {
            $purchase->item->name = mb_strlen($purchase->item->name) > 7
                ? mb_substr($purchase->item->name, 0, 7) . '...'
                : $purchase->item->name;
            return $purchase;
        });

        return view('chat',compact('chats', 'purchase', 'notBuyer', 'sellingItems', 'purchasingItems'));
    }

    public function sendMessage(Request $request, $purchase_id, $receiver_id)
    {
        // auth()->user() : 現在認証しているユーザーを取得
        $user = auth()->user();
        $now = Carbon::now();
        $validator = Validator::make(
            $request->all(),
            ['message' => 'required|string|max:400'],
            [
                'message.required' => 'メッセージを入力してください',
                'message.string' => 'メッセージの形式が不正です',
                'message.max' => 'メッセージは400文字以内で入力してください'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // メッセージをテーブルに保存
        try {
            $chat= Chat::create([
                'purchase_id' => $purchase_id,
                'sender_id' => $user->id,
                'is_read' => false,
                'message' => $request->input('message'),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'メッセージの保存に失敗'], 500);
        }

        // メッセージオブジェクトの作成
        $message = new Message;

        $message->chat_id = $chat->id;          // チャットID
        $message->receiver_id = $receiver_id;   // 受信者のユーザID
        $message->purchase_id = $purchase_id;   // 購入ID
        $message->username = $user->name;
        $message->message = $request->input('message');
        $message->datetime = $now->format('Y/m/d H:i');
        $message->image = $user->image;

        // メッセージ送信イベントを送信
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function read($purchase_id)
    {
        // リアルタイムで受信した場合に実行されるAPI
        // jsから呼び出される
        $user = auth()->user();

        try {
            Chat::query()
                ->where('purchase_id', $purchase_id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => '既読フラグの変更に失敗'], 500);        
        }
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $chat_id)
    {
        $validator = Validator::make(
            $request->all(),
            ['modified-message' => 'required|string|max:400'],
            [
                'modified-message.required' => 'メッセージを入力してください',
                'modified-message.string' => 'メッセージの形式が不正です',
                'modified-message.max' => 'メッセージは400文字以内で入力してください'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chat = Chat::find($chat_id);
            $chat->message = $request->input('modified-message');
            $chat->save();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'メッセージの更新に失敗'], 500);
        }

        return response()->json([
            'success' => true,
            'chatId' => $chat_id,
            'modifiedMessage' =>  $request->input('modified-message')
        ]);
    }

    public function delete($chat_id)
    {
        try {
            Chat::find($chat_id)
                ->update(['is_deleted' => true]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'メッセージの削除に失敗'], 500);
        }

        return response()->json([
            'success' => true,
            'chatId' => $chat_id,
        ]);
    }
}
