<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Category::exists()) {
            Category::insert([
                [
                    'category' => 'ファッション',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'category' => '家電',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'インテリア',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'レディース',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'メンズ',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'コスメ',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => '本',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'ゲーム',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'スポーツ',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'キッチン',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'ハンドメイド',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'アクセサリー',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'おもちゃ',
                    'created_at' => now(),
                    'updated_at' => now(),],
                [
                    'category' => 'ベビー・キッズ',
                    'created_at' => now(),
                    'updated_at' => now(),],
            ]);
        }
    }
}
