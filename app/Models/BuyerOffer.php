<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerOffer extends Model
{
    protected $fillable = [
        'buyer_request_id', 'user_id', 'quantity', 'price', 'availability_date',
        'message', 'status', 'listing_id'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(BuyerRequest::class, 'buyer_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
