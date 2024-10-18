<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');  // 各テスト開始時にDBをリフレッシュ
    }

    /**
     * A basic test example.
     *
     * @return void
     */
     public function test_商品一覧に全商品表示()
     {
        // Arrange
        $this->seed([
            UserSeeder::class,
            ConditionSeeder::class,
            ItemSeeder::class
        ]);

        $items = Item::all();

        // Act
        $response = $this->get('/');

        // Assert
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_購入後sold表示（おすすめにのみ表示されているか確認）()
    {
        // Arrange
        $buyer = $this->login();
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

        // Act
        $this->post('/purchase/'.$item->id, ['payment_method_id' => $payment->id]);
        $response = $this->get('/');

        // Assert
        $response->assertSeeInOrder([
            '<div class="tab first-tab">',
            '<p class="sold">'.$item->name.'</p>',
        ], false);
    }

     public function test_出品商品非表示()
    {
        // Arrange
        $user = $this->login();
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
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200)
            ->assertDontSee('abcdefg');
    }
}
