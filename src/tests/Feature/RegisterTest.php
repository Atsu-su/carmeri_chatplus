<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_名前未入力()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                    'name' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('お名前を入力してください');
    }

    public function test_メールアドレス未入力()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                    'email' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワード未入力()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                    'password' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('パスワードを入力してください');
    }

    public function test_パスワード7文字()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                    'password' => Str::random(7),
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('パスワードは8文字以上で入力してください');

        // Act
        $response = $this->from('/register')
        ->post('/register', [
                'password' => Str::random(8),
        ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertDontSee('パスワードは8文字以上で入力してください');
    }

    public function test_パスワード不一致()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                    'password' => 'abcdefghij',
                    'confirm_password' => 'ABCDEFGHIJ',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('パスワードと一致しません');
    }

    public function test_登録成功()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                    'name' => 'test',
                    'email' => 'email@e.com',
                    'password' => 'abcdefghij',
                    'confirm_password' => 'abcdefghij',
            ]);

        // Assert
        $response->assertStatus(302);
    }
}
