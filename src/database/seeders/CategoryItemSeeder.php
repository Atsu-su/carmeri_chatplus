<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryItem;
use Illuminate\Database\Seeder;

class CategoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! CategoryItem::exists()) {
            $max = Category::count();
            $cnt = 1;
            for ($i = 0; $i < 10; ++$i) {
                for ($j = 0; $j < 3; ++$j) {
                    CategoryItem::create([
                        'item_id' => $i + 1,
                        'category_id' => $cnt,
                    ]);
                    ++$cnt;
                    if ($cnt > $max) $cnt = 1;
                }
            }
        }
    }
}
