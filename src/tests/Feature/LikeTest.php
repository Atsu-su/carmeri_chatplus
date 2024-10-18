<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stringable;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class LikeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_いいね登録可能()
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
        $beforeComment = $beforeCrawler->filter('.item-detail-icons-like span')
            ->text();

        // Act
        $response = $this->post('/item/'.$item->id.'/like');

        // Assert
        $response->assertStatus(200)
                ->assertJson(['likeIt' => true]);

        $result = $this->get('/item/'.$item->id);
        $afterCrawler = new Crawler($result->content());
        $afterComment = $afterCrawler->filter('.item-detail-icons-like span')
            ->text();

        $this->assertEquals($beforeComment + 1, $afterComment);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_いいねアイコンの色変化()
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

        // 黄色に変化する
        // Act
        $response = $this->post('/item/'.$item->id.'/like');

        // Assert
        $response->assertStatus(200)
                ->assertJson(['likeIt' => true]);

        $this->get('/item/'.$item->id)
            ->assertSee('item-detail-icons-like filled');

        // 無色に変化する
        // Act
        $response = $this->post('/item/'.$item->id.'/like');

        // Assert
        $response->assertStatus(200)
                ->assertJson(['likeIt' => false]);

        $this->get('/item/'.$item->id)
            ->assertSee('item-detail-icons-like');
    }

    public function test_いいね解除可能()
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

        // いいね登録
        $this->post('/item/'.$item->id.'/like');
        $response = $this->get('/item/'.$item->id);
        $beforeCrawler = new Crawler($response->content());
        $beforeComment = $beforeCrawler->filter('.item-detail-icons-like span')
            ->text();

        // Act
        $response = $this->post('/item/'.$item->id.'/like');

        // Assert
        $response->assertStatus(200)
                ->assertJson(['likeIt' => false]);

        $result = $this->get('/item/'.$item->id);
        $afterCrawler = new Crawler($result->content());
        $afterComment = $afterCrawler->filter('.item-detail-icons-like span')
            ->text();

        $this->assertEquals($beforeComment - 1, $afterComment);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
