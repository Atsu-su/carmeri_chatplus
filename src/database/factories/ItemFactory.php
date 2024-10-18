<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /*
        * category_idはcategory_itemテーブルに登録されて
        * いるため不要
        */

        return [
            'seller_id' => $this->faker->numberBetween(1, 10),
            'on_sale' => true,
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 10000),
            'brand' => $this->faker->word(),
            'condition_id' => $this->faker->numberBetween(1, 4),
            'description' => $this->faker->realText(50),
            'image' => 'image.jpg',
            // 'image' => $this->faker->imageUrl(320, 240, 'fashion', true),
        ];
    }

    public function notOnSale() {
        return $this->state(function (){
            return [
                'on_sale' => false,
            ];
        });
    }
}
