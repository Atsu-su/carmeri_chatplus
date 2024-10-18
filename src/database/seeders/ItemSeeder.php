<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Item::exists()) {
            // Item::factory(10)->notOnSale()->create();
            Item::insert([
                [
                    'name' => '腕時計',
                    'price' => 15000,
                    'description' => 'スタイリッシュなデザインのメンズ腕時計',
                    'image' => 'Armani+Mens+Clock.jpg',
                    'condition_id' => 1,
                    'seller_id' => 1,
                    'brand' => 'アルマーニ'
                ],
                [
                    'name' => 'HDD',
                    'price' => 5000,
                    'description' => '高速で信頼性の高いハードディスク',
                    'image' => 'HDD+Hard+Disk.jpg',
                    'condition_id' => 2,
                    'seller_id' => 2,
                    'brand' => '日田日立'                ],
                [
                    'name' => '玉ねぎ3束',
                    'price' => 300,
                    'description' => '新鮮な玉ねぎ3束のセット',
                    'image' => 'iLoveIMG+d.jpg',
                    'condition_id' => 3,
                    'seller_id' => 3,
                    'brand' => '春日井農園',
                ],
                [
                    'name' => '革靴',
                    'price' => 4000,
                    'description' => 'クラシックなデザインの革靴',
                    'image' => 'Leather+Shoes+Product+Photo.jpg',
                    'condition_id' => 4,
                    'seller_id' => 4,
                    'brand' => '牛の皮',
                ],
                [
                    'name' => 'ノートPC',
                    'price' => 45000,
                    'description' => '高性能なノートパソコン',
                    'image' => 'Living+Room+Laptop.jpg',
                    'condition_id' => 1,
                    'seller_id' => 5,
                    'brand' => 'NEEC',
                ],
                [
                    'name' => 'マイク',
                    'price' => 8000,
                    'description' => '高音質のレコーディング用マイク',
                    'image' => 'Music+Mic+4632231.jpg',
                    'condition_id' => 2,
                    'seller_id' => 6,
                    'brand' => 'マイクロマイク',
                ],
                [
                    'name' => 'ショルダーバッグ',
                    'price' => 3500,
                    'description' => 'おしゃれなショルダーバッグ',
                    'image' => 'Purse+fashion+pocket.jpg',
                    'condition_id' => 3,
                    'seller_id' => 7,
                    'brand' => 'ぐっちっち',
                ],
                [
                    'name' => 'タンブラー',
                    'price' => 500,
                    'description' => '使いやすいタンブラー',
                    'image' => 'Tumbler+souvenir.jpg',
                    'condition_id' => 4,
                    'seller_id' => 8,
                    'brand' => '金属探知機',
                ],
                [
                    'name' => 'コーヒーミル',
                    'price' => 4000,
                    'description' => '手動のコーヒーミル',
                    'image' => 'Waitress+with+Coffee+Grinder.jpg',
                    'condition_id' => 1,
                    'seller_id' => 9,
                    'brand' => '久保田',
                ],
                [
                    'name' => 'メイクセット',
                    'price' => 2500,
                    'description' => '便利なメイクアップセット',
                    'image' => 'makeup+set.jpg',
                    'condition_id' => 2,
                    'seller_id' => 10,
                    'brand' => '魚油産業',
                ],

            ]);
          }
    }
}
