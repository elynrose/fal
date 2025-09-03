<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Album;
use App\Models\PhotoModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoEditDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $album;
    protected $photo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->album = Album::factory()->create(['user_id' => $this->user->id]);
        
        Storage::fake('public');
        
        $this->photo = PhotoModel::factory()->create([
            'album_id' => $this->album->id,
            'name' => 'Test Photo',
            'description' => 'Test Description',
            'image_path' => 'photos/test-image.jpg',
            'status' => 'pending'
        ]);
    }

    public function test_user_can_view_edit_form()
    {
        $response = $this->actingAs($this->user)->get(route('photos.edit', $this->photo));
        
        $response->assertStatus(200);
        $response->assertSee('Edit Photo');
        $response->assertSee('Test Photo');
        $response->assertSee('Test Description');
        $response->assertSee('Update Photo');
    }

    public function test_user_can_update_photo_model()
    {
        $response = $this->actingAs($this->user)->put(route('photos.update', $this->photo), [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description'
        ]);
        
        $response->assertRedirect(route('photos.show', $this->photo));
        $response->assertSessionHas('success', 'Photo updated successfully!');
        
        $this->photo->refresh();
        $this->assertEquals('Updated Photo Name', $this->photo->name);
        $this->assertEquals('Updated Description', $this->photo->description);
    }

    public function test_user_cannot_update_other_users_photo()
    {
        $otherUser = User::factory()->create();
        $otherAlbum = Album::factory()->create(['user_id' => $otherUser->id]);
        $otherPhoto = PhotoModel::factory()->create([
            'album_id' => $otherAlbum->id,
            'name' => 'Other Photo',
            'description' => 'Other Description',
            'image_path' => 'photos/other-image.jpg',
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($this->user)->put(route('photos.update', $otherPhoto), [
            'name' => 'Hacked Name',
            'description' => 'Hacked Description'
        ]);
        
        $response->assertStatus(403);
    }

    public function test_user_can_delete_photo_model()
    {
        $response = $this->actingAs($this->user)->delete(route('photos.destroy', $this->photo));
        
        $response->assertRedirect(route('albums.show', $this->album));
        $response->assertSessionHas('success', 'Photo deleted successfully!');
        
        $this->assertDatabaseMissing('photo_models', ['id' => $this->photo->id]);
    }

    public function test_user_cannot_delete_other_users_photo()
    {
        $otherUser = User::factory()->create();
        $otherAlbum = Album::factory()->create(['user_id' => $otherUser->id]);
        $otherPhoto = PhotoModel::factory()->create([
            'album_id' => $otherAlbum->id,
            'name' => 'Other Photo',
            'description' => 'Other Description',
            'image_path' => 'photos/other-image.jpg',
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($this->user)->delete(route('photos.destroy', $otherPhoto));
        
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('photo_models', ['id' => $otherPhoto->id]);
    }

    public function test_edit_form_validation()
    {
        $response = $this->actingAs($this->user)->put(route('photos.update', $this->photo), [
            'name' => '',
            'description' => 'Valid description'
        ]);
        
        $response->assertSessionHasErrors(['name']);
        
        $this->photo->refresh();
        $this->assertEquals('Test Photo', $this->photo->name);
    }

    public function test_edit_form_shows_current_values()
    {
        $response = $this->actingAs($this->user)->get(route('photos.edit', $this->photo));
        
        $response->assertStatus(200);
        $response->assertSee('Test Photo');
        $response->assertSee('Test Description');
        $response->assertSee('Current Photo');
    }

    public function test_delete_confirmation_in_ui()
    {
        $response = $this->actingAs($this->user)->get(route('photos.show', $this->photo));
        
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
        $newImage = UploadedFile::fake()->image('new-photo.jpg');
        
        $response = $this->actingAs($this->user)->put(route('photos.update', $this->photo), [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description',
            'photo' => $newImage
        ]);
        
        $response->assertRedirect(route('photos.show', $this->photo));
        $response->assertSessionHas('success', 'Photo updated successfully! The new photo will need to be trained.');
        
        $this->photo->refresh();
        $this->assertEquals('Updated Photo Name', $this->photo->name);
        $this->assertEquals('Updated Description', $this->photo->description);
        $this->assertEquals('pending', $this->photo->status);
        
        Storage::disk('public')->assertExists($this->photo->image_path);
    }

    public function test_user_can_update_without_replacing_photo()
    {
        $response = $this->actingAs($this->user)->put(route('photos.update', $this->photo), [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description'
        ]);
        
        $response->assertRedirect(route('photos.show', $this->photo));
        $response->assertSessionHas('success', 'Photo updated successfully!');
        
        $this->photo->refresh();
        $this->assertEquals('Updated Photo Name', $this->photo->name);
        $this->assertEquals('Updated Description', $this->photo->description);
    }

    public function test_photo_replacement_validates_file_type()
    {
        Storage::fake('public');
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);
        
        $response = $this->actingAs($this->user)->put(route('photos.update', $this->photo), [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description',
            'photo' => $invalidFile
        ]);
        
        $response->assertSessionHasErrors(['photo']);
        
        $this->photo->refresh();
        $this->assertEquals('Test Photo', $this->photo->name);
    }

    public function test_photo_replacement_validates_file_size()
    {
        Storage::fake('public');
        $largeImage = UploadedFile::fake()->image('large-photo.jpg')->size(11000);
        
        $response = $this->actingAs($this->user)->put(route('photos.update', $this->photo), [
            'name' => 'Updated Photo Name',
            'description' => 'Updated Description',
            'photo' => $largeImage
        ]);
        
        $response->assertSessionHasErrors(['photo']);
        
        $this->photo->refresh();
        $this->assertEquals('Test Photo', $this->photo->name);
    }

    public function test_edit_form_includes_file_upload_field()
    {
        $response = $this->actingAs($this->user)->get(route('photos.edit', $this->photo));
        
        $response->assertStatus(200);
        $response->assertSee('Replace Photo (Optional)');
        $response->assertSee('Upload new photo');
        $response->assertSee('multipart/form-data');
        $response->assertSee('image/*');
    }
}
