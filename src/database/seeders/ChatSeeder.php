<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Item;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Chat::exists()) {
            $faker = Faker::create('ja_JP');

            $max = 3;
            for($i = 1; $i <= 5; ++$i ) {
                $item = Item::find($i);

                for ($j = 0; $j < $max; ++$j) {
                    // 購入者
                    Chat::insert([
                        [
                            'purchase_id' => $i,
                            'sender_id' => $i + 1,
                            'is_read' => false,
                            'message' => $faker->realText(100),
                            'created_at' => now()->addMinutes($j),
                            'updated_at' => now()->addMinutes($j)
                        ],
                    ]);

                    // 出品者
                    Chat::insert([
                        [
                            'purchase_id' => $i,
                            'sender_id' => $item->seller_id,
                            'is_read' => false,
                            'message' => $faker->realText(100),
                            'created_at' => now()->addMinutes($j),
                            'updated_at' => now()->addMinutes($j)
                        ],
                    ]);
                }
                ++$max;
            }

            $max = 8;
            for($i = 1; $i <= 3; ++$i ) {
                $item = Item::find($i);

                for ($j = 0; $j < $max; ++$j) {
                    // 購入者
                    Chat::insert([
                        [
                            'purchase_id' => $i + 5,
                            'sender_id' => $i + 1,
                            'is_read' => false,
                            'message' => $faker->realText(100),
                            'created_at' => now()->addMinutes($j),
                            'updated_at' => now()->addMinutes($j)
                        ],
                    ]);

                    // 出品者
                    Chat::insert([
                        [
                            'purchase_id' => $i + 5,
                            'sender_id' => $item->seller_id,
                            'is_read' => false,
                            'message' => $faker->realText(100),
                            'created_at' => now()->addMinutes($j),
                            'updated_at' => now()->addMinutes($j)
                        ],
                    ]);
                }
                ++$max;
            }
        }
    }
}
