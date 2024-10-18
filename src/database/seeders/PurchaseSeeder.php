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

        for ($i = 1; $i <= 3; ++$i) {
            Purchase::create([
                'item_id' => $i,
                'buyer_id' => $faker->numberBetween(1, 10),
                'payment_method_id' => $faker->numberBetween(1, 2),
        ]);}
    }
}
