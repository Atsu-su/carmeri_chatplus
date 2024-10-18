<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 実行順序を制御できます
        $this->call([
          CategorySeeder::class,
          ConditionSeeder::class,
          UserSeeder::class,
          ItemSeeder::class,
          CategoryItemSeeder::class,
          PaymentMethodSeeder::class,
          PurchaseSeeder::class,
          CommentSeeder::class,
          LikeSeeder::class,
      ]);
    }
}
