<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo_model_id',
        'theme_id',
        'image_path',
        'prompt_used',
        'generation_parameters',
        'generation_id',
        'status',
        'error_message',
        'generated_at'
    ];

    protected $casts = [
        'generation_parameters' => 'array',
        'generated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photoModel(): BelongsTo
    {
        return $this->belongsTo(PhotoModel::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
