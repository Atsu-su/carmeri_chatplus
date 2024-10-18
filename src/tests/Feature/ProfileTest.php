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

class ProfileTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_プロフィール表示()
    {
        // Arrange
        $user = $this->login();

        // Act
        $response = $this->get('mypage/profile');

        // Assert
        $response->assertStatus(200)
            ->assertSee($user->image)
            ->assertSee($user->name)
            ->assertSee($user->postal_code)
            ->assertSee($user->address)
            ->assertSee($user->building_name);
    }
}
