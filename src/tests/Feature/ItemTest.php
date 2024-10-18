<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class ItemTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_詳細情報表示()
    {
        // Arrange
        $user = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);

        $item = Item::factory()->create(
            [
                'seller_id' => $user->id,
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

        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'this is a comment',
        ]);

        $selectedItem = Item::with(['condition', 'comments'])->first();
        $comment = Comment::with('user')->first();

        // Act
        $response = $this->get('/item/'.$selectedItem->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee($selectedItem->name)
            ->assertSee(number_format($selectedItem->price))
            ->assertSee('<span id="number-of-likes">1</span>', false)
            ->assertSeeInOrder([
                '<div class="item-detail-icons-icon item-detail-icons-comment">',
                '1',
            ], false)
            ->assertSee($selectedItem->brand)
            ->assertSee($selectedItem->condition->condition)
            ->assertSee($comment->user->name)
            ->assertSee($comment->comment)
            ->assertSee('<img src="'. asset('storage/item_images/'.$selectedItem->image).'" width="600" height="600"', false);
    }

    public function test_複数カテゴリ表示()
    {
        // Arrange
        $user = User::factory()->create();
        $condition = Condition::create(['condition' => '新品、未使用']);

        $item = Item::factory()->create(
            [
                'seller_id' => $user->id,
                'on_sale' => true,
                'name' => 'abcdefg',
                'price' => 1000,
                'brand' => 'brand',
                'condition_id' => $condition->id,
                'description' => 'this is a pen',
                'image' => 'image.jpg',
            ]
        );

        $category1 = Category::create(['category' => 'category1']);
        $category2 = Category::create(['category' => 'category2']);

        CategoryItem::create([
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);
        CategoryItem::create([
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);

        $selectedItem = Item::with('categoryItems.category')->first();

        // Act
        $response = $this->get('/item/'.$selectedItem->id);

        // Assert
        $response->assertStatus(200);
        foreach($selectedItem->categoryItems as $categoryItem) {
            $response->assertSee($categoryItem->category->category);
        }
    }
}
