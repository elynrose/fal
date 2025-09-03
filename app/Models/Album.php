<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_word',
        'user_id',
        'status',
        'model_id'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    /**
     * Get the user that owns the album.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photos for the album.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PhotoModel::class);
    }

    /**
     * Check if the album has any trained photos.
     */
    public function hasTrainedPhotos(): bool
    {
        return $this->photos()->where('status', 'completed')->exists();
    }

    /**
     * Get the album status based on its photos.
     */
    public function getOverallStatus(): string
    {
        $photos = $this->photos;
        
        if ($photos->isEmpty()) {
            return 'empty';
        }
        
        if ($photos->where('status', 'completed')->count() === $photos->count()) {
            return 'completed';
        }
        
        if ($this->photos()->where('status', 'training')->exists()) {
            return 'training';
        }
        
        if ($this->photos()->where('status', 'failed')->exists()) {
            return 'failed';
        }
        
        return 'pending';
    }
}
