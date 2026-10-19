<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'full_name', 'email', 'phone', 'country', 'province', 'district', 'primary_interest', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function sentEnquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'user_id');
    }

    public function receivedEnquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'recipient_id');
    }

    public function buyerOffers(): HasMany
    {
        return $this->hasMany(BuyerOffer::class, 'user_id');
    }

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'country',
        'province',
        'district',
        'primary_interest',
        'role',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
        ];
    }
}
