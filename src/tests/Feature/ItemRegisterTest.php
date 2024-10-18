<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class ItemRegisterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_商品出品()
    {
        // Arrange
        $user = $this->login();
        $seller = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);
        $category = Category::create(['category' => 'ファッション']);
        $fakeItem = Item::factory()->make();

        // テスト用ストレージの作成
        Storage::fake('public');
        Storage::disk('public')->makeDirectory('item_images');

        // アップロードファイルの作成
        $file = new UploadedFile(
            base_path('tests/test_images/Armani+Mens+Clock.jpg'),
            'Armani+Mens+Clock.jpg',
            'image/jpeg',
            null,
            true
        );

        // Act
        $response = $this->post('/sell',[
            'seller_id' => $seller->id,
            'name' => $fakeItem->name,
            'price' => $fakeItem->price,
            'brand' => $fakeItem->brand,
            'condition_id' => $condition->id,
            'category_id' => [$category->id,],
            'description' => $fakeItem->description,
            'image' => $file,
        ]);

        // 画像は名前が変更されるので、登録された商品情報を取得
        $registeredItem = Item::first();

        // dd($registeredItem->image);

        // Assert
        $response->assertStatus(302);
        Storage::disk('public')->assertExists('item_images/'.$registeredItem->image);
        $this->get('/item/'.$registeredItem->id)
            ->assertSee($registeredItem->image)
            ->assertSee($fakeItem->name)
            ->assertSee(number_format($fakeItem->price))
            ->assertSee($fakeItem->brand)
            ->assertSee($condition->condition)
            ->assertSee($category->category);
    }
}
