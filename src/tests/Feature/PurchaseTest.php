<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stringable;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_商品購入()
    {
        // Arrange
        $seller = User::factory()->create();
        $buyer = $this->login();
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

        // Assert
        $this->assertDatabaseHas('purchases', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method_id' => $payment->id,
            'status' => 'processing',
        ]);
    }

    public function test_購入後sold表示()
    {
        // Arrange
        $seller = User::factory()->create();
        $this->login();
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
        $this->post('/purchase/'. $item->id, ['payment_method_id' => $payment->id]);
        $response = $this->get('/');

        // Assert
        $response->assertSeeInOrder([
            '<div class="tab first-tab">',
            '<p class="sold">'.$item->name.'</p>',
        ], false);
    }

    public function test_購入後購入した商品一覧へ追加されているか確認()
    {
        // Arrange
        $seller = User::factory()->create();
        $this->login();
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
        $this->post('/purchase/'. $item->id, ['payment_method_id' => $payment->id]);
        $response = $this->get('/mypage');

        // Assert
        $response->assertSeeInOrder([
            '<div class="tab first-tab">',
            '<p class="sold">'.$item->name.'</p>',
        ], false);
    }

    public function test_商品購入後プロフィール画面に表示()
    {
        // Arrange
        $seller = User::factory()->create();
        $this->login();
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

        // 購入
        // Act
        $this->post('/purchase/'.$item->id, ['payment_method_id' => $payment->id]);

        // Assert
        $this->get('/mypage')
            ->assertSeeInOrder([
                '<div class="tab second-tab js-hidden">',
                '<p class="sold">'.$item->name.'</p>'
            ], false);
    }
}
