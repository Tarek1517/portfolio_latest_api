<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'image',
        'order',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->image, '/storage/')) {
            return env('APP_URL') . $this->image;
        }
        return env('APP_URL') . '/storage/' . $this->image;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
