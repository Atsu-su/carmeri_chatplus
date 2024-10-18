<?php

namespace Tests\Feature;

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

class ProfileEditTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_プロフィール編集()
    {
        // Arrange
        $user = $this->login();
        $newInfo = User::factory()->make();

        // テスト用ストレージの作成
        Storage::fake('public');
        Storage::disk('public')->makeDirectory('profile_images');

        // アップロードファイルの作成
        $file = new UploadedFile(
            base_path('tests/test_images/test2.jpg'),
            'test2.jpg',
            'image/jpeg',
            null,
            true
        );

        // Act
        $response = $this->post('mypage/profile',[
            'is_changed' => true,
            'image' => $file,
            'name' => $newInfo->name,
            'postal_code' => $newInfo->postal_code,
            'address' => $newInfo->address,
            'building_name' => $newInfo->building_name,
        ]);

        $newUser = User::find($user->id);

        // Assert
        $response->assertStatus(302);
        Storage::disk('public')->assertExists('profile_images/'.$newUser->image);
        $this->get('mypage/profile')
            ->assertSee($newUser->image)
            ->assertSee($newUser->name)
            ->assertSee($newUser->postal_code)
            ->assertSee($newUser->address)
            ->assertSee($newUser->building_name);
    }
}
