<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UIImprovementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed themes for testing
        $this->seed(\Database\Seeders\ThemeSeeder::class);
    }

    public function test_dashboard_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Welcome back');
        $response->assertSee('Quick Actions');
        $response->assertSee('Recent Albums');
        $response->assertSee('stats-card');
        $response->assertSee('action-card');
    }

    public function test_albums_index_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/albums');
        
        $response->assertStatus(200);
        $response->assertSee('My Albums');
        $response->assertSee('Create New Album');
        $response->assertSee('No albums yet');
        $response->assertSee('Create Your First Album');
    }

    public function test_themes_index_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/themes');
        
        $response->assertStatus(200);
        $response->assertSee('Available Themes');
        $response->assertSee('Choose from our collection of themes');
        $response->assertSee('Corporate');
    }

    public function test_generations_index_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/generations');
        
        $response->assertStatus(200);
        $response->assertSee('Generated Images');
        $response->assertSee('Generate New Image');
        $response->assertSee('No generated images yet');
    }

    public function test_login_page_has_modern_design()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertSee('Sign In');
        $response->assertSee('form-control');
        $response->assertSee('btn-primary');
    }

    public function test_register_page_has_modern_design()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('Create Account');
        $response->assertSee('form-control');
        $response->assertSee('btn-primary');
    }

    public function test_welcome_page_has_modern_design()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Train Your Photos with AI');
        $response->assertSee('How It Works');
        $response->assertSee('stats-icon');
    }

    public function test_navigation_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('navbar');
        $response->assertSee('Albums');
        $response->assertSee('Themes');
        $response->assertSee('Generated Images');
    }
}
