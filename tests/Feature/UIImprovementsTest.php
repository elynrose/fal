<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UIImprovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Welcome back');
        $response->assertSee('Quick Actions');
        $response->assertSee('Recent Activity');
        $response->assertSee('stats-card');
        $response->assertSee('action-card');
    }

    public function test_photos_index_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/photos');
        
        $response->assertStatus(200);
        $response->assertSee('My Photo Models');
        $response->assertSee('Upload New Photo');
        $response->assertSee('No photo models yet');
        $response->assertSee('Upload Your First Photo');
    }

    public function test_themes_index_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/themes');
        
        $response->assertStatus(200);
        $response->assertSee('Available Themes');
        $response->assertSee('Choose from our collection of themes');
    }

    public function test_generations_index_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/generations');
        
        $response->assertStatus(200);
        $response->assertSee('Generated Images');
        $response->assertSee('Generate New Image');
        $response->assertSee('No generated images yet');
        $response->assertSee('Generate Your First Image');
    }

    public function test_login_page_has_modern_design()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
        $response->assertSee('Sign in to your AI Photo Trainer account');
        $response->assertSee('form-control');
        $response->assertSee('btn btn-primary');
    }

    public function test_register_page_has_modern_design()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('Create Account');
        $response->assertSee('Join AI Photo Trainer and start creating amazing images');
        $response->assertSee('form-control');
        $response->assertSee('btn btn-primary');
    }

    public function test_welcome_page_has_modern_design()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Train Your Photos with AI');
        $response->assertSee('How It Works');
        $response->assertSee('stats-icon');
        $response->assertSee('btn btn-primary');
    }

    public function test_navigation_has_modern_design()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('navbar');
        $response->assertSee('AI Photo Trainer');
        $response->assertSee('fa-camera');
        $response->assertSee('fa-home');
        $response->assertSee('fa-images');
        $response->assertSee('fa-magic');
        $response->assertSee('fa-palette');
    }
}
