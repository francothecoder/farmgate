<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerReview extends Model
{
    protected $fillable = ['buyer_offer_id', 'seller_id', 'reviewer_id', 'rating', 'body'];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(BuyerOffer::class, 'buyer_offer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
