<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientApp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'category',
        'description',
        'access_area_id',
        'is_active',
        'oauth_client_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accessArea()
    {
        return $this->belongsTo(AccessArea::class);
    }

    public function passportClient()
    {
        return $this->belongsTo(\Laravel\Passport\Client::class, 'oauth_client_id', 'id');
    }

    public function hasPassportClient(): bool
    {
        return !empty($this->oauth_client_id);
    }
}

