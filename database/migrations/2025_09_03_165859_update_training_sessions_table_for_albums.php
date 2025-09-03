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
        Schema::table('training_sessions', function (Blueprint $table) {
            // Add album_id foreign key
            $table->foreignId('album_id')->nullable()->constrained()->onDelete('cascade');
            
            // Remove old photo_model_id field
            $table->dropForeign(['photo_model_id']);
            $table->dropColumn('photo_model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            // Remove album_id
            $table->dropForeign(['album_id']);
            $table->dropColumn('album_id');
            
            // Restore old photo_model_id field
            $table->foreignId('photo_model_id')->constrained()->onDelete('cascade');
        });
    }
};
