<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhotoModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'album_id',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    /**
     * Get the user that owns the photo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the album that owns the photo.
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Get the training sessions for the photo.
     */
    public function trainingSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    /**
     * Get the generated images for the photo.
     */
    public function generatedImages(): HasMany
    {
        return $this->hasMany(GeneratedImage::class);
    }
}
