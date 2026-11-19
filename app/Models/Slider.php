<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Slider extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function image(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : null,
            set: fn ($value) => $value,
        );
    }
}
