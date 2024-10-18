<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_メールアドレス未入力()
    {
        // Act
        $response = $this->from('/login')
            ->post('/login', [
                    'email' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/login');

        $this->followRedirects($response)
            ->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワード未入力()
    {
        // Act
        $response = $this->from('/login')
            ->post('/login', [
                    'password' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/login');

        $this->followRedirects($response)
            ->assertSee('パスワードを入力してください');
    }

    public function test_認証失敗()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        // パスワードが間違っている
        $response = $this->from('/login')
            ->post('/login', [
                    'email' => $user->email,
                    'password' => 'aaaaaaaa',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/login');

        $this->followRedirects($response)
            ->assertSee('ログイン情報が登録されていません');

        // Act
        // メールアドレスが間違っている
        $response = $this->from('/login')
        ->post('/login', [
                'email' => 'aaaa@aaaa.com',
                'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/login');

        $this->followRedirects($response)
            ->assertSee('ログイン情報が登録されていません');
    }

    public function test_認証成功()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->from('/login')
            ->post('/login', [
                    'email' => $user->email,
                    'password' => 'password',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/');

        $this->assertAuthenticated();
    }
}
