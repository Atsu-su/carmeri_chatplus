<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;
use App\Messages\Session as MessageSession;
use Illuminate\Http\Request;

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
        $listedItems = Item::query()
            ->where('seller_id', $user->id)
            ->get();
        $purchasedItems = Purchase::query()
            ->with('item:id,name,image')
            ->where('buyer_id', $user->id)
            ->get();
        $message = MessageSession::exists('message');

        return view('mypage', compact('user', 'listedItems', 'purchasedItems', 'message'));
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
