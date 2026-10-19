<?php

namespace App\Models;

use App\Models\Concerns\HasModerationEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KnowledgeArticle extends Model
{
    use HasModerationEvents;

    protected $fillable = [
        'category_id', 'title', 'slug', 'summary', 'body', 'featured_image',
        'author_id', 'reviewer_id', 'status', 'published_at', 'reading_time',
        'seo_title', 'seo_description', 'tags', 'is_featured',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(KnowledgeComment::class, 'knowledge_article_id');
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        return Str::startsWith($this->featured_image, ['http://', 'https://'])
            ? $this->featured_image
            : Storage::disk('public')->url($this->featured_image);
    }

    public function getRenderedBodyAttribute(): string
    {
        $body = preg_replace('/<br\s*\/?>/i', "\n", $this->body) ?? $this->body;
        $body = preg_replace('/<\/(?:p|div|h[1-6]|li|blockquote|ul|ol)>/i', "\n\n", $body) ?? $body;

        return Str::markdown(trim(strip_tags($body)), ['html_input' => 'strip']);
    }
}
