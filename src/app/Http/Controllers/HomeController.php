<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;
use App\Messages\Session as MessageSession;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();

            $items = Item::query()
                ->where('seller_id', '!=', $user->id)
                ->orderBy('id', 'desc')
                ->get();

            $likedItems = Like::query()
                ->with('item')
                ->where('user_id', $user->id)
                ->whereHas('item', function ($query) use ($user) {
                    $query->where('seller_id', '!=', $user->id);
                })
                ->orderBy('item_id', 'desc')
                ->get();

            return view('index', compact('items', 'likedItems'));
        } else {
            $items = Item::orderBy('id', 'desc')->get();
            return view('index', compact('items'));
        }
    }

    public function myPageIndex()
    {
        $user = auth()->user();
        $user->rating = $user->evaluations > 0 ? round($user->rating_sum / $user->evaluations) : 0;
        $listedItems = Item::query()
            ->where('seller_id', $user->id)
            ->get();
        $purchasedItems = Purchase::query()
            ->with('item:id,name,image')
            ->where('buyer_id', $user->id)
            ->get();
        $message = MessageSession::exists('message');

        // 1. 出品者の場合
        // ・purchasesテーブルにあるレコードのうち、自分が出品した商品の情報と
        //   purchase_idを取得する

        $sellingItems = Purchase::query()
            ->with(['item:id,name,price,image', 'chats' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                    ->where('is_read', false);
            }])
            ->whereHas('item', function ($query) use ($user) {
                $query->where('seller_id', $user->id);
            })
            ->whereHas('chats')
            ->select('purchases.id', 'purchases.item_id')
            ->leftJoinSub(function ($query) use ($user) {
                $query->from('chats')
                    ->select('purchase_id', DB::raw('MAX(created_at) as latest_chat'))
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $user->id)
                    ->groupBy('purchase_id');
            }, 'latest_chats', 'purchases.id', '=', 'latest_chats.purchase_id')
            ->orderByRaw("COALESCE(latest_chats.latest_chat, '1900-01-01') DESC")
            ->get();

        // 2. 購入者の場合
        // ・purchasesテーブルのレコードのうち、自分が購入した商品の情報と
        //   purchase_idを取得する

        $purchasingItems = Purchase::query()
            ->with(['item:id,name,price,image', 'chats' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                    ->where('is_read', false);
            }])
            ->where('buyer_id', $user->id)
            ->whereHas('chats')
            ->select('purchases.id', 'purchases.item_id')
            ->leftJoinSub(function ($query) use ($user) {
                $query->from('chats')
                    ->select('purchase_id', DB::raw('MAX(created_at) as latest_chat'))
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $user->id)
                    ->groupBy('purchase_id');
            }, 'latest_chats', 'purchases.id', '=', 'latest_chats.purchase_id')
            ->orderByRAW("COALESCE(latest_chats.latest_chat, '1900-01-01') DESC")
            ->get();

        return view('mypage',
            compact(
                'user',
                'listedItems',
                'purchasedItems',
                'sellingItems',
                'purchasingItems',
                'message'
            )
        );
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if (auth()->check()) {
            $user = auth()->user();

            $items = Item::query()
                ->where('name', 'like', "%$keyword%")
                ->where('seller_id', '!=', $user->id)
                ->orderBy('id', 'desc')
                ->get();

            $likedItems = Like::query()
                ->with('item')
                ->where('user_id', $user->id)
                ->whereHas('item', function ($query) use ($keyword, $user) {
                    $query->where('name', 'like', "%$keyword%")
                          ->where('seller_id', '!=', $user->id);
                })
                ->orderBy('item_id', 'desc')
                ->get();

            return view('index', compact('items', 'likedItems', 'keyword'));
        } else {
            $items = Item::query()
            ->where('name', 'like', "%$keyword%")
            ->orderBy('id', 'desc')
            ->get();

            return view('index', compact('items', 'keyword'));
        }
    }
}
