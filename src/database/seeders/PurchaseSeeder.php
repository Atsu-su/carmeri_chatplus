<?php

namespace Database\Seeders;

use App\Models\Purchase;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;


class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 5; ++$i) {
            Purchase::create([
                'item_id' => $i,
                'buyer_id' => $i + 1,
                'payment_method_id' => $faker->numberBetween(1, 2),
                'status' => 'processing',
        ]);}

        for ($i = 6; $i <= 10; ++$i) {
            Purchase::create([
                'item_id' => $i,
                'buyer_id' => $i - 4,
                'payment_method_id' => $faker->numberBetween(1, 2),
                'status' => 'processing',
        ]);}
    }
}
