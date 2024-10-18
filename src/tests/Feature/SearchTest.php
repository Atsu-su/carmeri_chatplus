<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_検索された商品が表示()
    {
        // Arrange
        $user = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);

        Item::factory()->create(
            [
                'seller_id' => $user->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'description',
                'image' => 'image.jpg',
            ]
        );

        // Act
        $response = $this->post('/', ['keyword' => 'cd']);

        // Assert
        $response->assertStatus(200)
            ->assertSeeInOrder([
                '<div class="tab first-tab">',
                'abcdefg',
                '<div class="tab second-tab js-hidden">',
            ], false);
    }

    public function test_検索された商品がマイリストにも表示()
    {
        // Arrange
        $user = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $login = $this->login();

        $item = Item::factory()->create(
            [
                'seller_id' => $user->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'description',
                'image' => 'image.jpg',
            ]
        );

        Like::factory()->create(
            [
                'user_id' => $login->id,
                'item_id' => $item->id,
            ]
        );

        // Act
        $response = $this->post('/', ['keyword' => 'cd']);

        // Assert
        $response->assertStatus(200)
            ->assertSeeInOrder([
                '<div class="tab second-tab js-hidden">',
                'abcdefg',
            ], false);
    }
}
