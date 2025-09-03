<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PhotoModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoEditDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $photo;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and photo for testing
        $this->user = User::factory()->create();
        
        // Mock storage
        Storage::fake('public');
        
        // Create a photo model
        $this->photo = PhotoModel::create([
            'user_id' => $this->user->id,
            'name' => 'Test Photo',
            'description' => 'Test Description',
            'image_path' => 'photos/test-image.jpg',
            'status' => 'pending'
        ]);
    }

    public function test_user_can_view_edit_form()
    {
        $response = $this->actingAs($this->user)->get("/photos/{$this->photo->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSee('Edit Photo Model');
        $response->assertSee('Test Photo');
        $response->assertSee('Test Description');
        $response->assertSee('Update Photo Model');
    }

    public function test_user_can_update_photo_model()
    {
        $response = $this->actingAs($this->user)->put("/photos/{$this->photo->id}", [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description'
        ]);
        
        $response->assertRedirect(route('photos.show', $this->photo));
        $response->assertSessionHas('success', 'Photo model updated successfully!');
        
        $this->photo->refresh();
        $this->assertEquals('Updated Photo Name', $this->photo->name);
        $this->assertEquals('Updated Description', $this->photo->description);
    }

    public function test_user_cannot_update_other_users_photo()
    {
        $otherUser = User::factory()->create();
        $otherPhoto = PhotoModel::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Photo',
            'description' => 'Other Description',
            'image_path' => 'photos/other-image.jpg',
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($this->user)->put("/photos/{$otherPhoto->id}", [
            'name' => 'Hacked Name',
            'description' => 'Hacked Description'
        ]);
        
        $response->assertStatus(403);
    }

    public function test_user_can_delete_photo_model()
    {
        $response = $this->actingAs($this->user)->delete("/photos/{$this->photo->id}");
        
        $response->assertRedirect(route('photos.index'));
        $response->assertSessionHas('success', 'Photo model deleted successfully!');
        
        $this->assertDatabaseMissing('photo_models', ['id' => $this->photo->id]);
    }

    public function test_user_cannot_delete_other_users_photo()
    {
        $otherUser = User::factory()->create();
        $otherPhoto = PhotoModel::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Photo',
            'description' => 'Other Description',
            'image_path' => 'photos/other-image.jpg',
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($this->user)->delete("/photos/{$otherPhoto->id}");
        
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('photo_models', ['id' => $otherPhoto->id]);
    }

    public function test_edit_form_validation()
    {
        $response = $this->actingAs($this->user)->put("/photos/{$this->photo->id}", [
            'name' => '', // Empty name should fail validation
            'description' => 'Valid description'
        ]);
        
        $response->assertSessionHasErrors(['name']);
        
        $this->photo->refresh();
        $this->assertEquals('Test Photo', $this->photo->name); // Should not be updated
    }

    public function test_edit_form_shows_current_values()
    {
        $response = $this->actingAs($this->user)->get("/photos/{$this->photo->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSee('Test Photo');
        $response->assertSee('Test Description');
        $response->assertSee('Current Photo');
    }

    public function test_delete_confirmation_in_ui()
    {
        $response = $this->actingAs($this->user)->get("/photos/{$this->photo->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Delete');
        $response->assertSee('return confirm');
    }

    public function test_edit_button_in_photos_index()
    {
        $response = $this->actingAs($this->user)->get('/photos');
        
        $response->assertStatus(200);
        $response->assertSee('Edit');
        $response->assertSee('Delete');
        $response->assertSee('fa-edit');
        $response->assertSee('fa-trash');
    }

    public function test_user_can_replace_photo_file()
    {
        Storage::fake('public');
        
        // Create a fake image file
        $newImage = UploadedFile::fake()->image('new-photo.jpg');
        
        $response = $this->actingAs($this->user)->put("/photos/{$this->photo->id}", [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description',
            'photo' => $newImage
        ]);
        
        $response->assertRedirect(route('photos.show', $this->photo));
        $response->assertSessionHas('success', 'Photo model updated successfully! The new photo will need to be trained.');
        
        $this->photo->refresh();
        $this->assertEquals('Updated Photo Name', $this->photo->name);
        $this->assertEquals('Updated Description', $this->photo->description);
        $this->assertEquals('pending', $this->photo->status); // Status should be reset
        $this->assertNull($this->photo->model_id); // Model ID should be cleared
        
        // Check that new file was stored
        Storage::disk('public')->assertExists($this->photo->image_path);
    }

    public function test_user_can_update_without_replacing_photo()
    {
        $response = $this->actingAs($this->user)->put("/photos/{$this->photo->id}", [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description'
            // No photo file provided
        ]);
        
        $response->assertRedirect(route('photos.show', $this->photo));
        $response->assertSessionHas('success', 'Photo model updated successfully!');
        
        $this->photo->refresh();
        $this->assertEquals('Updated Photo Name', $this->photo->name);
        $this->assertEquals('Updated Description', $this->photo->description);
        $this->assertEquals('pending', $this->photo->status); // Status should remain unchanged
        $this->assertEquals('photos/test-image.jpg', $this->photo->image_path); // Path should remain unchanged
    }

    public function test_photo_replacement_validates_file_type()
    {
        Storage::fake('public');
        
        // Create a fake non-image file
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);
        
        $response = $this->actingAs($this->user)->put("/photos/{$this->photo->id}", [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description',
            'photo' => $invalidFile
        ]);
        
        $response->assertSessionHasErrors(['photo']);
        
        $this->photo->refresh();
        $this->assertEquals('Test Photo', $this->photo->name); // Should not be updated
    }

    public function test_photo_replacement_validates_file_size()
    {
        Storage::fake('public');
        
        // Create a fake image file that's too large (over 10MB)
        $largeImage = UploadedFile::fake()->image('large-photo.jpg')->size(11000); // 11MB
        
        $response = $this->actingAs($this->user)->put("/photos/{$this->photo->id}", [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description',
            'photo' => $largeImage
        ]);
        
        $response->assertSessionHasErrors(['photo']);
        
        $this->photo->refresh();
        $this->assertEquals('Test Photo', $this->photo->name); // Should not be updated
    }

    public function test_edit_form_includes_file_upload_field()
    {
        $response = $this->actingAs($this->user)->get("/photos/{$this->photo->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSee('Replace Photo (Optional)');
        $response->assertSee('Upload new photo');
        $response->assertSee('multipart/form-data');
        $response->assertSee('image/*');
    }
}
