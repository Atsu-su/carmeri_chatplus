<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggleLike($item_id)
    {
        $user = auth()->user();

        // いいねの登録・登録解除を行う
        $like = Like::query()
            ->where('item_id', $item_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $like) {
            // 新規登録（いいねがなかった場合）
            Like::create([
                'item_id' => $item_id,
                'user_id' => $user->id,
            ]);

            $likeIt = true; // true: いいねをする
        } else {
            Like::destroy($like->id);

            $likeIt = false; // false: いいねを解除
        }

        return response()->json(['likeIt' => $likeIt]);
    }
}
