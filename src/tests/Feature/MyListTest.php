<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class MyListTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_お気に入り商品表示()
    {
        // Arrange
        $user = $this->login();
        $users = User::factory(2)->create();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $firstItem = Item::factory()->create([
            'seller_id' => $users[0]->id,
            'condition_id' => $condition->id,
        ]);
        $secondItem = Item::factory()->create([
            'seller_id' => $users[1]->id,
            'condition_id' => $condition->id,
        ]);

        Like::factory()->create(
            [
                'user_id' => $user->id,
                'item_id' => $secondItem->id,
            ]
        );

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200)
            ->assertSeeInOrder([
                '<div class="tab first-tab">',
                $firstItem->name,
                '<div class="tab second-tab js-hidden">',
            ], false)
            ->assertSeeInOrder([
                '<div class="tab second-tab js-hidden">',
                $secondItem->name,
            ], false);
    }

    public function test_購入後sold表示（マイリストにのみ表示されているか確認）()
    {
        // Arrange
        $user = $this->login();
        $seller = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $payment = PaymentMethod::create(['payment_method' => 'クレジットカード']);
        $item = Item::factory()->create(
            [
                'seller_id' => $seller->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'this is a pen',
                'image' => 'Armani+Mens+Clock.jpg',
            ]
        );

        Like::factory()->create(
            [
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]
        );

        // Act
        $this->post('/purchase/'.$item->id, ['payment_method_id' => $payment->id]);
        $response = $this->get('/');

        // Assert
        $response->assertSeeInOrder([
            '<div class="tab second-tab js-hidden">',
            '<p class="sold">'.$item->name.'</p>',
        ], false);
    }

    public function test_出品した商品が非表示()
    {
        // Arrange
        $seller = $this->login();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $item = Item::factory()->create(
            [
                'seller_id' => $seller->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'this is a pen',
                'image' => 'Armani+Mens+Clock.jpg',
            ]
        );

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertDontSee($item->name);
    }

    public function test_未ログイン時商品非表示()
    {
        // Arrange
        $user = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);
        Item::factory(3)->create([
            'seller_id' => $user->id,
            'condition_id' => $condition->id,
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200)
            ->assertSeeInOrder([
                '<div class="tab second-tab js-hidden">',
                'ログイン</a>後に表示されます',
            ], false);
    }
}
