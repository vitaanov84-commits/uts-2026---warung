<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AuthPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_response_includes_photo_field(): void
    {
        $user = User::factory()->create([
            'name' => 'Pembeli Test',
            'email' => 'pembeli@test.com',
            'password' => 'password123',
            'role' => 'pembeli',
            'photo' => 'photos/profile.jpg',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
            'role' => 'pembeli',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.photo', 'photos/profile.jpg');
    }

    public function test_seeded_default_users_can_login(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@warung.test',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.role', 'admin');
    }

    public function test_user_can_login_without_role_field(): void
    {
        $user = User::factory()->create([
            'name' => 'Penjaga Test',
            'email' => 'penjaga@test.com',
            'password' => 'password123',
            'role' => 'penjaga',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.role', 'penjaga');
    }

    public function test_user_can_upload_photo(): void
    {
        $user = User::factory()->create([
            'name' => 'Pembeli Test',
            'email' => 'photo@test.com',
            'password' => 'password123',
            'role' => 'pembeli',
        ]);

        $response = $this->postJson('/api/profile/photo', [
            'user_id' => $user->id,
            'photo' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertOk();
        $this->assertNotNull($user->fresh()->photo);
    }
}
