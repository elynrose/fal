<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        // Drop FK on photo_model_id safely (PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE training_sessions DROP CONSTRAINT IF EXISTS training_sessions_photo_model_id_foreign');
        } else {
            // Fallback for other drivers: attempt drop using Schema (may fail harmlessly if not present)
            Schema::table('training_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('training_sessions', 'photo_model_id')) {
                    $table->dropForeign(['photo_model_id']);
                }
            });
        }

        // Drop photo_model_id column if present
        Schema::table('training_sessions', function (Blueprint $table) {
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
        });

        // Recreate photo_model_id column and FK
        Schema::table('training_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('training_sessions', 'photo_model_id')) {
                $table->foreignId('photo_model_id')->constrained()->onDelete('cascade');
            }
        });
    }
};
