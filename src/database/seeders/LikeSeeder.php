<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 5) as $index) {
            for ($i = 0; $i < 3; ++$i) {
                DB::table('likes')->insert([
                    'item_id' => $index,
                    'user_id' => $i + $index,
                ]);
            }
        }
    }
}
