<?php

namespace App\Messages;

class Message
{
    // 定数としてメッセージタイプを定義
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const INFO = 'info';

    // メッセージ定義
    private static array $messages = [
        'purchase' => [
            'success' => [
                'status' => self::SUCCESS,
                'title' => 'お買上げありがとうございます',
                'contents' => [
                    '購入手続きが正常に完了しました',
                    '詳細は購入履歴からご確認頂けます'
                ],
            ],

            'failed' => [
                'status' => self::ERROR,
                'title' => '購入処理に失敗しました',
                'contents' => [
                    '申し訳ございません',
                    'お手数ですが、しばらく時間をおいて再度お試しください'
                ],
            ],

            'already' => [
                'status' => self::ERROR,
                'title' => '購入手続きを継続できません',
                'contents' => ['直前に他のお客様にて購入手続きが開始されました'],
            ],

            'cancel' => [
                'status' => self::INFO,
                'title' => '購入処理をキャンセルしました',
                'contents' => ['引き続き、お買い物をお楽しみください'],
            ],
        ],

        'address' => [
            'success' => [
                'status' => self::SUCCESS,
                'title' => '住所変更完了',
                'contents' => ['住所を変更しました'],
            ],
            'failed' => [
                'status' => self::ERROR,
                'title' => '住所変更に失敗しました',
                'contents' => ['申し訳ございません', 'お手数ですが、しばらく時間をおいて再度お試しください'],
            ],
        ],

        'comment' => [
            'failed' => [
                'status' => self::ERROR,
                'title' => 'コメントの登録に失敗しました',
                'contents' => ['申し訳ございません', 'お手数ですが、しばらく時間をおいて再度お試しください'],
            ],
        ],

        'profile' => [
            'success' => [
                'status' => self::SUCCESS,
                'title' => 'プロフィール更新完了',
                'contents' => ['プロフィールを更新しました'],
            ],

            'failed' => [
                'status' => self::ERROR,
                'title' => 'プロフィールの更新に失敗しました',
                'contents' => ['申し訳ございません', 'お手数ですが、しばらく時間をおいて再度お試しください'],
            ],
        ],

        'list' => [
            'success' => [
                'status' => self::SUCCESS,
                'title' => '出品完了',
                'contents' => ['商品が出品されました', '出品した商品はマイページから確認できます'],
            ],

            'failed' => [
                'status' => self::ERROR,
                'title' => '出品に失敗しました',
                'contents' => ['申し訳ございません', 'お手数ですが、しばらく時間をおいて再度お試しください'],
            ],
        ],
    ];

    // メッセージを取得するメソッド
    public static function get(string $key): ?array
    {
        $keys = explode('.', $key);
        $message = self::$messages;

        foreach ($keys as $k) {
            if (!isset($message[$k])) {
                return null;
            }
            $message = $message[$k];
        }

        return $message;
    }
}
