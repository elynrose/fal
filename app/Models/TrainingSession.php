<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'album_id',
        'session_id',
        'status',
        'training_config',
        'training_results',
        'error_message',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'training_config' => 'array',
        'training_results' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
