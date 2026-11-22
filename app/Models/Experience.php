<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'company_name',
        'address',
        'start_date',
        'end_date',
        'status',
        'type',
        'company_logo',
        'languages',
        'heighlights'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
        'languages'   => 'array',
        'heighlights'   => 'array',
    ];

    protected $appends = ['company_logo_url'];

    public function getCompanyLogoUrlAttribute(): ?string
    {
        return $this->company_logo ? env('APP_URL') . '/storage/' . $this->company_logo : null;
    }
}
