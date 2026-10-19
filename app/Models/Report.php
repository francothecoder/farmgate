<?php

namespace App\Models;

use App\Models\Concerns\HasModerationEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasModerationEvents;

    protected $fillable = ['user_id', 'reportable_type', 'reportable_id', 'reason', 'details', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
