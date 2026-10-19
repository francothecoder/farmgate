<?php

namespace App\Models\Concerns;

use App\Models\ModerationEvent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasModerationEvents
{
    public function moderationEvents(): MorphMany
    {
        return $this->morphMany(ModerationEvent::class, 'subject')->latest();
    }
}
