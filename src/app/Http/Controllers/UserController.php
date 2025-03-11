<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RatingRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // （chat view）
    // ・[done] ボタンクリックでモーダルが開く
    // ・送信で評価を送る
    //  (purchasecontroller)
    // ・[done] 取引完了ボタン押下でAPIでpurchaseテーブルのstatusをcompleteに変更する
    //  (usercontroller)
    // ・評価を送るボタンで評価を保存する（total_rating, total_evaluations）
    //  (homecontroller)
    // ・purchasesテーブルのstatusがprocessingの時表示
    //  (mypage view)
    // ・評価を計算する

    public function rating(RatingRequest $request, $seller_id)
    {
        // $seller_idは出品者のID
        $user = User::find($seller_id);

        try {
            $user->update([
                'rating_sum' => $user->rating_sum + $request->input('rating'),
                'evaluations' => $user->evaluations + 1,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            return redirect()->route('home');
        }
    }
}
