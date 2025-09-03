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
        
        // Check if generated_images table exists and has proper foreign keys
        if (Schema::hasTable('generated_images')) {
            Schema::table('generated_images', function (Blueprint $table) {
                // Drop existing foreign keys if they exist (PostgreSQL specific)
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                
                try {
                    $table->dropForeign(['photo_model_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                
                try {
                    $table->dropForeign(['theme_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
            });

            // Re-add foreign key constraints
            Schema::table('generated_images', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('photo_model_id')->references('id')->on('photo_models')->onDelete('cascade');
                $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
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
