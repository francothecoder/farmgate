<?php

namespace App\Models;

use App\Models\Concerns\HasModerationEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Business extends Model
{
    use HasModerationEvents;

    protected $fillable = [
        'user_id', 'name', 'business_type', 'description', 'logo', 'cover_image',
        'phone', 'email', 'website', 'country_id', 'province_id', 'district_id',
        'display_address', 'opening_hours', 'products_services', 'status', 'verified_at',
        'verification_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'business_type');
    }
}
