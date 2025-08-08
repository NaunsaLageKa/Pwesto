<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_profile_image()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        
        $response = $this->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '1234567890',
            'profile_image' => $file,
        ]);
        
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'profile_image' => 'profile_images/' . $file->hashName(),
        ]);
        
        Storage::disk('public')->assertExists('profile_images/' . $file->hashName());
    }

    public function test_user_can_update_profile_without_image()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '1234567890',
        ]);
        
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_profile_image_validation()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Test with invalid file type
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $response = $this->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '1234567890',
            'profile_image' => $file,
        ]);
        
        $response->assertSessionHasErrors(['profile_image']);
    }
}
