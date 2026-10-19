<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BuyerOffer extends Model
{
    protected $fillable = [
        'buyer_request_id', 'user_id', 'quantity', 'price', 'availability_date',
        'message', 'status', 'listing_id', 'response_message', 'responded_at',
    ];

    protected function casts(): array
    {
        return ['availability_date' => 'date', 'responded_at' => 'datetime'];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(BuyerRequest::class, 'buyer_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sellerReview(): HasOne
    {
        return $this->hasOne(SellerReview::class, 'buyer_offer_id');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
