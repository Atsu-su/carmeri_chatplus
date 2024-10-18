<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_住所表示()
    {
        // Arrange
        $user = $this->login();
        $seller = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $item = Item::factory()->create([
                'seller_id' => $seller->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'this is a pen',
                'image' => 'Armani+Mens+Clock.jpg',
        ]);

        // Act
        $response = $this->get('/purchase/address/'.$item->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee($user->postal_code)
            ->assertSee($user->address)
            ->assertSee($user->building_name);
    }

    public function test_住所更新及び商品購入()
    {
        // Arrange
        $seller = User::factory()->create();
        $this->login();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $payment = PaymentMethod::create(['payment_method' => 'クレジットカード']);
        $item = Item::factory()->create([
                'seller_id' => $seller->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'this is a pen',
                'image' => 'Armani+Mens+Clock.jpg',
        ]);

        // Act
        // 住所変更
        $newAddress = User::factory()->make();
        $this->post('/purchase/address/'.$item->id, [
            'postal_code' => $newAddress->postal_code,
            'address' => $newAddress->address,
            'building_name' => $newAddress->building_name,
        ]);

        // 商品購入
        $this->post('/purchase/'.$item->id, ['payment_method_id' => $payment->id]);

        $updatedBuyer = Purchase::with('user')->where('item_id', $item->id)
            ->first()
            ->user;

        // Assert
        $this->assertSame($newAddress->postal_code, $updatedBuyer->postal_code);
        $this->assertSame($newAddress->address, $updatedBuyer->address);
        $this->assertSame($newAddress->building_name, $updatedBuyer->building_name);
    }
}
