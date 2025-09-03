<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\PhotoModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoModelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $album;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->album = Album::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('public');
    }

    public function test_user_can_view_photo_models_index()
    {
        $response = $this->actingAs($this->user)->get('/photos');
        $response->assertStatus(200);
        $response->assertViewIs('photos.index');
    }

    public function test_user_can_view_photo_upload_form()
    {
        $response = $this->actingAs($this->user)->get('/photos/create');
        $response->assertStatus(200);
        $response->assertViewIs('photos.create');
    }

    public function test_user_can_upload_photo()
    {
        $file = UploadedFile::fake()->image('test-photo.jpg');

        $response = $this->actingAs($this->user)->post(route('photos.store'), [
            'album_id' => $this->album->id,
            'name' => 'Test Photo',
            'description' => 'A test photo for AI training',
            'photos' => [$file]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('photo_models', [
            'album_id' => $this->album->id,
            'name' => 'Test Photo - test-photo.jpg',
            'description' => 'A test photo for AI training',
            'status' => 'pending'
        ]);
    }

    public function test_user_can_upload_multiple_photos()
    {
        $files = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg'),
            UploadedFile::fake()->image('photo3.jpg')
        ];

        $response = $this->actingAs($this->user)->post(route('photos.store'), [
            'album_id' => $this->album->id,
            'name' => 'Multiple Photos',
            'description' => 'Multiple photos for AI training',
            'photos' => $files
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('photo_models', [
            'album_id' => $this->album->id,
            'name' => 'Multiple Photos - photo1.jpg',
            'description' => 'Multiple photos for AI training',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('photo_models', [
            'album_id' => $this->album->id,
            'name' => 'Multiple Photos - photo2.jpg',
            'description' => 'Multiple photos for AI training',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('photo_models', [
            'album_id' => $this->album->id,
            'name' => 'Multiple Photos - photo3.jpg',
            'description' => 'Multiple photos for AI training',
            'status' => 'pending'
        ]);
    }

    public function test_photo_upload_requires_authentication()
    {
        $file = UploadedFile::fake()->image('test-photo.jpg');

        $response = $this->post(route('photos.store'), [
            'album_id' => $this->album->id,
            'name' => 'Test Photo',
            'photos' => [$file]
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_photo_upload_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('photos.store'), []);

        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors(['photos']);
    }

    public function test_photo_upload_validates_image_file()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)->post(route('photos.store'), [
            'album_id' => $this->album->id,
            'name' => 'Test Photo',
            'photos' => [$file]
        ]);

        $response->assertSessionHasErrors(['photos.0']);
    }

    public function test_photo_upload_validates_multiple_image_files()
    {
        $files = [
            UploadedFile::fake()->image('valid-photo.jpg'),
            UploadedFile::fake()->create('invalid-document.pdf', 100),
            UploadedFile::fake()->image('another-valid-photo.jpg')
        ];

        $response = $this->actingAs($this->user)->post(route('photos.store'), [
            'album_id' => $this->album->id,
            'name' => 'Test Photos',
            'photos' => $files
        ]);

        $response->assertSessionHasErrors(['photos.1']);
    }

    public function test_user_can_view_their_photo_model()
    {
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $this->album->id
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.show', $photoModel));

        $response->assertStatus(200);
        $response->assertViewIs('photos.show');
        $response->assertViewHas('photo', $photoModel);
    }

    public function test_user_cannot_view_other_users_photo_model()
    {
        $otherUser = User::factory()->create();
        $otherAlbum = Album::factory()->create(['user_id' => $otherUser->id]);
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $otherAlbum->id
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.show', $photoModel));

        $response->assertStatus(403);
    }

    public function test_user_can_edit_their_photo_model()
    {
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $this->album->id
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.edit', $photoModel));

        $response->assertStatus(200);
        $response->assertViewIs('photos.edit');
    }

    public function test_user_can_update_their_photo_model()
    {
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $this->album->id
        ]);

        $response = $this->actingAs($this->user)->put(route('photos.update', $photoModel), [
            'name' => 'Updated Photo Name',
            'description' => 'Updated description'
        ]);

        $response->assertRedirect(route('photos.show', $photoModel));
        $this->assertDatabaseHas('photo_models', [
            'id' => $photoModel->id,
            'name' => 'Updated Photo Name',
            'description' => 'Updated description'
        ]);
    }

    public function test_user_can_delete_their_photo_model()
    {
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $this->album->id
        ]);

        $response = $this->actingAs($this->user)->delete(route('photos.destroy', $photoModel));

        $response->assertRedirect();
        $this->assertDatabaseMissing('photo_models', ['id' => $photoModel->id]);
    }

    public function test_photo_model_has_correct_relationships()
    {
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $this->album->id
        ]);

        $this->assertEquals($this->album->id, $photoModel->album_id);
        $this->assertEquals($this->user->id, $photoModel->album->user_id);
    }

    public function test_photo_model_status_enum_values()
    {
        $photoModel = PhotoModel::factory()->create([
            'album_id' => $this->album->id,
            'status' => 'pending'
        ]);

        $this->assertEquals('pending', $photoModel->status);

        $photoModel->update(['status' => 'training']);
        $this->assertEquals('training', $photoModel->status);

        $photoModel->update(['status' => 'completed']);
        $this->assertEquals('completed', $photoModel->status);

        $photoModel->update(['status' => 'failed']);
        $this->assertEquals('failed', $photoModel->status);
    }
}
