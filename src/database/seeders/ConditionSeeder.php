<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Condition::exists()) {
            Condition::insert([
                [
                    'condition' => '良好',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'condition' => '目立った傷や汚れなし',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'condition' => 'やや傷や汚れあり',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'condition' => '状態が悪い',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
