<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'order_number',
        'massage',
        'image',
        'status',
        'rating',
        'profession',
        'project_id'
    ];

    public function image(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ? Storage::url($value) : null,
            set: fn($value) => $value,
        );
    }


    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
