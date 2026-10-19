<?php

namespace App\Models;

use App\Models\Concerns\HasModerationEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasModerationEvents;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description', 'quantity', 'unit',
        'price', 'currency', 'negotiable', 'country_id', 'province_id', 'district_id',
        'location_detail', 'condition', 'availability', 'status', 'contact_preference',
        'featured_image', 'rejection_reason', 'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class);
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        return Str::startsWith($this->featured_image, ['http://', 'https://'])
            ? $this->featured_image
            : Storage::disk('public')->url($this->featured_image);
    }
}
