<?php

namespace App\Models;

use App\Models\Concerns\HasModerationEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerRequest extends Model
{
    use HasModerationEvents;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'quantity', 'unit', 'target_price',
        'quality_requirements', 'location', 'required_date', 'description', 'status',
        'country_id', 'province_id', 'district_id', 'contact_preference',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(BuyerOffer::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'related_id')
            ->where('related_type', 'buyer_request');
    }
}
