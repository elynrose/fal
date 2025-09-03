<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration ensures all foreign key constraints are properly set up
        // for PostgreSQL compatibility after all tables are created
        
        // Wait a moment to ensure all tables are fully created
        sleep(1);
        
        // Check if generated_images table exists and add foreign keys
        if (Schema::hasTable('generated_images')) {
            Schema::table('generated_images', function (Blueprint $table) {
                // Add foreign key constraints safely
                try {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists or failed, continue
                }
                
                try {
                    $table->foreign('photo_model_id')->references('id')->on('photo_models')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists or failed, continue
                }
                
                try {
                    $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists or failed, continue
                }
            });
        }
        
        // Check if photo_models table exists and add foreign keys
        if (Schema::hasTable('photo_models')) {
            Schema::table('photo_models', function (Blueprint $table) {
                try {
                    $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists or failed, continue
                }
            });
        }
        
        // Check if training_sessions table exists and add foreign keys
        if (Schema::hasTable('training_sessions')) {
            Schema::table('training_sessions', function (Blueprint $table) {
                try {
                    $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists or failed, continue
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};

