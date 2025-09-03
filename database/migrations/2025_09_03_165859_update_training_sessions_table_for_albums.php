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
            // Add album_id if it doesn't exist
            if (!Schema::hasColumn('training_sessions', 'album_id')) {
                $table->foreignId('album_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            // Drop FK on photo_model_id if present
            try {
                if (Schema::hasColumn('training_sessions', 'photo_model_id')) {
                    $table->dropForeign(['photo_model_id']);
                }
            } catch (\Throwable $e) {
                // Ignore if FK doesn't exist
            }
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            // Drop photo_model_id column if present
            if (Schema::hasColumn('training_sessions', 'photo_model_id')) {
                $table->dropColumn('photo_model_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            // Remove album_id if present
            if (Schema::hasColumn('training_sessions', 'album_id')) {
                try {
                    $table->dropForeign(['album_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('album_id');
            }

            // Recreate photo_model_id column and FK
            if (!Schema::hasColumn('training_sessions', 'photo_model_id')) {
                $table->foreignId('photo_model_id')->constrained()->onDelete('cascade');
            }
        });
    }
};
