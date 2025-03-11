<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use App\Http\Requests\PurchaseRequest;
use App\Messages\Session as MessageSession;
use App\Messages\Message;
use Exception;
use GuzzleHttp\Psr7\Message as Psr7Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function index($item_id)
    {
        $user = auth()->user();
        $item = Item::with('purchase')->findOrFail($item_id);
        $message = MessageSession::exists('message');
        return view('purchase', compact('user', 'item', 'message'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $user = auth()->user();

        try {
            DB::beginTransaction();

            // nameのみ必要
            $item = Item::query()
                ->where('id', $item_id)
                ->where('on_sale', true)
                ->lockForUpdate()
                ->first();

            if (!$item) {
                return back()->with('message', Message::get('purchase.already'));
            }

            $item->update(['on_sale' => false]);

            $purchase = Purchase::create([
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'payment_method_id' => $request->input('payment_method_id'),
                'status' => 'processing',
            ]);

            DB::commit();

            return $this->stripe($item, $user, $purchase);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return back()->with('message', Message::get('purchase.failed'));
        }
    }

    public function stripe(Item $item, User $user, Purchase $purchase)
    {
        Stripe::setApiKey(config('stripe.stripe_secret_key'));
        $session = Session::create([
            // 必要最小限の情報のみ
            'metadata' => [
                'user_id' => $user->id,
                'order_id' => $purchase->id
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name
                    ],
                    'unit_amount' => $item->price
                ],
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['purchase_id' => $purchase->id]),
            'cancel_url' => route('payment.cancel', ['purchase_id' => $purchase->id]),
        ]);

        return redirect($session->url);
    }

    public function success($purchase_id)
    {
        $item = Purchase::query()
            ->where('id', $purchase_id)
            ->first();

        try {
            $item->update(['status' => 'purchased']);
        } catch (Exception $e) {
            Log::error('==========お客様支払い完了後のDB更新に失敗==========');
            Log::error('purchasesテーブルのstatusがprocessingのままです');
            Log::error('purchasesテーブルの情報');
            Log::error('id: '. $item->id . ' user_id: '. $item->buyer_id . ' item_id: '. $item->item_id);
            Log::error($e->getMessage());
            Log::error('=================================================');
        }

        return redirect()
            ->route('mypage')
            ->with('message', Message::get('purchase.success'));
    }

    public function cancel($purchase_id)
    {
            $item = Purchase::query()
            ->with('item')
            ->where('id', $purchase_id)
            ->first();

        try {
            $item->delete();
            $item->item->update(['on_sale' => true]);
        } catch (Exception $e) {
            Log::error('==========お客様支払い完了後のDB更新に失敗==========');
            Log::error('purchasesテーブルのstatusがprocessingのままの可能性があります');
            Log::error('itemsテーブルのon_saleが0（false）のままの可能性があります');
            Log::error('purchasesテーブルの情報');
            Log::error('id: '. $item->id . ' user_id: '. $item->buyer_id . ' item_id: '. $item->item_id);
            Log::error($e->getMessage());
            Log::error('=================================================');
        }

        return redirect()
            ->route('mypage')
            ->with('message', Message::get('purchase.cancel'));
    }

    public function complete(Request $request, $purchase_id)
    {
        // notBuyer:true => 出品者が取引完了ボタンを押した
        // notBuyer:false => 購入者が取引完了ボタンを押した
        $input = [
            'purchaseId' => $purchase_id,
            'notBuyer' => $request->query('notBuyer'),
            'receiverId' => $request->query('receiverId')
        ];

        $rules = [
            'purchaseId' => 'required|integer|exists:purchases,id',
            'notBuyer' => 'required|boolean',
            'receiverId' => 'required|integer'
        ];

        $messages = [
            'purchaseId.required' => '値がありません',
            'purchaseId.integer' => '値が不正です',
            'purchaseId.exists' => '取引情報が見つかりません',
            'notBuyer.required' => '値がありません',
            'notBuyer.boolean' => '値が不正です',
            'receiverId.required' => '値がありません',
            'receiverId.integer' => '値が不正です'
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 時間があれば実装
        //
        // $user = auth()->user();
        // $notBuyer = $request->query('notBuyer');    // true: 自分が出品者, false: 自分が購入者
        // $receiverId = $request->query('receiverId');
        // if ($notBuyer) {
        //     // 出品者
        //     // 対象の商品が自分が出品し相手が購入しているか確認
        //     try {
        //         Purchase::query()
        //             ->whereHas('item', function ($query) use ($receiverId, $user) {
        //                 $query->where('seller_id', $user->id);
        //             })
        //             ->where('id', $purchase_id)
        //             ->where('buyer_id', $receiverId)
        //             ->findOrFail();
        //     } catch (Exception $e) {
        //         abort(403);
        //     }
        // } else {
        //     // 購入者
        //     // 対象の商品が自分が購入し相手が出品しているか確認
        //     try {
        //         Purchase::query()
        //             ->whereHas('item', function ($query) use ($receiverId) {
        //                 $query->where('seller_id', $receiverId);
        //             })
        //             ->where('buyer_id', $user->id)
        //             ->where('id', $purchase_id)
        //             ->findOrFail();
        //     } catch (Exception $e) {
        //         abort(403);
        //     }
        // }

        try {
            Purchase::query()
                ->where('id', $purchase_id)
                ->update(['status' => 'complete']);
            return response()->json(['success' => true]);
        } catch(Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}
