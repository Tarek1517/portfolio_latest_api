<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class HeroSection extends Model
{
    protected $fillable = [
        'title',
        'tagline',
        'sub_title',
        'description',
        'order_number',
        'status',
        'key_features',
        'image'
    ];

    protected $casts = [
        'key_features' => 'array',
    ];

        public function image(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ? Storage::url($value) : null,
            set: fn($value) => $value,
        );
    }
}
