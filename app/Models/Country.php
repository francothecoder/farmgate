<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'code'];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
