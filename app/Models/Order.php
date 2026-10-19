<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'buyer_offer_id',
        'buyer_id',
        'seller_id',
        'quantity',
        'unit',
        'total_price',
        'currency',
        'fulfillment_method',
        'fulfillment_location',
        'fulfillment_notes',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'total_price' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(BuyerOffer::class, 'buyer_offer_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(OrderUpdate::class)->latest();
    }
}
