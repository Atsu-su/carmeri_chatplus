<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_ログアウト成功()
    {
        // Arrange
        $this->login();

        // Act
        $response = $this->from('/')
            ->post('/logout');

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/');
    }
}