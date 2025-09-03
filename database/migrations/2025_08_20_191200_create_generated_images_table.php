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
        Schema::create('generated_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('photo_model_id');
            $table->unsignedBigInteger('theme_id');
            $table->string('image_path');
            $table->text('prompt_used');
            $table->json('generation_parameters')->nullable();
            $table->string('generation_id')->nullable(); // fal generation ID
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        // Foreign key constraints will be added by the safety migration
        // after all tables are created to avoid dependency issues
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_images');
    }
};
