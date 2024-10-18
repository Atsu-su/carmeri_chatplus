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
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class CommentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_ログイン時コメント投稿可能()
    {
        // Arrange
        $user = $this->login();
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

        $response = $this->get('/item/'.$item->id);
        $beforeCrawler = new Crawler($response->content());
        $beforeComment = $beforeCrawler->filter('.item-detail-icons-comment span')
            ->text();

        // Act
        $response = $this->from('/item/'.$item->id)
            ->post('/item/'.$item->id.'/comment',
                ['comment' => 'This is a comment.']
            );

        // Assert
        $response->assertStatus(302)
                ->assertRedirect('/item/'.$item->id);

        $result = $this->get('/item/'.$item->id);
        $afterCrawler = new Crawler($result->content());
        $afterComment = $afterCrawler->filter('.item-detail-icons-comment span')
            ->text();

        $result->assertSee('This is a comment.');
        $this->assertEquals($beforeComment + 1, $afterComment);
    }

    public function test_未ログイン時コメント投稿不可()
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

        // Act
        $response = $this->get('/item/'.$item->id);

        // Assert
        $response->assertSee('<p class="item-detail-comment-login">コメントをするには<a href="http://localhost/login">ログイン</a>が必要です。</p>', false);
    }

    public function test_コメント未入力()
    {
        // Arrange
        $user = $this->login();
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

        // Act
        $response = $this->from('/item/'.$item->id)
            ->post('/item/'.$item->id.'/comment',
                ['comment' => '']
            );

        // Assert
        $response->assertStatus(302)
                ->assertRedirect('/item/'.$item->id);

        $this->followRedirects($response)
            ->assertSee('コメントを入力してください');
    }

    public function test_コメント文字数超過()
    {
        // Arrange
        $user = $this->login();
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

        // Act
        $response = $this->from('/item/'.$item->id)
            ->post('/item/'.$item->id.'/comment',
                ['comment' => Str::random(256)]
            );

        // Assert
        $response->assertStatus(302)
                ->assertRedirect('/item/'.$item->id);

        $this->followRedirects($response)
            ->assertSee('コメントは255文字以内で入力してください');
    }
}
