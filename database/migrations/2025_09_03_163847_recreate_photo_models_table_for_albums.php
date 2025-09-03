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
        // Drop the old table
        Schema::dropIfExists('photo_models');
        
        // Create the new table with album structure
        Schema::create('photo_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'training', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('photo_models');
        
        // Recreate the old table structure
        Schema::create('photo_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'training', 'completed', 'failed'])->default('pending');
            $table->string('model_id')->nullable();
            $table->json('training_metadata')->nullable();
            $table->timestamps();
        });
    }
};
