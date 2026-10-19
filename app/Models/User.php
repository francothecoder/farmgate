<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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

    public function knowledgeComments(): HasMany
    {
        return $this->hasMany(KnowledgeComment::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function sellerReviews(): HasMany
    {
        return $this->hasMany(SellerReview::class, 'seller_id');
    }

    public function sellerVerifier(): BelongsTo
    {
        return $this->belongsTo(self::class, 'seller_verified_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function getWhatsAppUrlAttribute(): ?string
    {
        $digits = preg_replace('/\D+/', '', $this->phone ?? '');

        if (! $digits) {
            return null;
        }

        if (Str::startsWith($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (Str::startsWith($digits, '0')) {
            $digits = '260'.substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $digits = '260'.$digits;
        }

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode('Hello, I found your listing on FarmGate and would like to know more.');
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
            'seller_verified_at' => 'datetime',
        ];
    }
}
