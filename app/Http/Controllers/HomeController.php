<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\KnowledgeArticle;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredCategories = Schema::hasTable('categories') ? Category::query()->limit(6)->get() : collect();
        $featuredListings = Schema::hasTable('listings') ? Listing::with(['category', 'user', 'images'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->latest()
            ->limit(6)
            ->get() : collect();
        $buyerRequests = Schema::hasTable('buyer_requests') ? BuyerRequest::with(['category', 'user'])
            ->where('status', 'approved')
            ->latest()
            ->limit(4)
            ->get() : collect();
        $articles = Schema::hasTable('knowledge_articles') ? KnowledgeArticle::with(['category', 'author'])
            ->where('status', 'published')
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get() : collect();
        $businesses = Schema::hasTable('businesses') ? Business::query()->where('status', 'approved')->latest()->limit(4)->get() : collect();
        $homeSlides = $this->homeSlides($featuredListings, $articles, $businesses);

        return view('home', compact(
            'featuredCategories',
            'featuredListings',
            'buyerRequests',
            'articles',
            'businesses',
            'homeSlides'
        ));
    }

    private function homeSlides(Collection $listings, Collection $articles, Collection $businesses): Collection
    {
        $slides = collect();

        foreach ($listings as $listing) {
            $image = $listing->featured_image_url ?? $listing->images->first()?->url;

            if ($image) {
                $slides->push([
                    'image' => $image,
                    'alt' => $listing->title,
                    'label' => 'Marketplace',
                    'title' => $listing->title,
                    'description' => Str::limit($listing->description, 130),
                    'url' => route('listings.show', ['listing' => $listing->slug]),
                    'link_label' => 'Explore listing',
                ]);
            }
        }

        foreach ($articles as $article) {
            if ($article->featured_image_url) {
                $slides->push([
                    'image' => $article->featured_image_url,
                    'alt' => $article->title,
                    'label' => 'Knowledge hub',
                    'title' => $article->title,
                    'description' => $article->summary ?? Str::limit(strip_tags($article->body), 130),
                    'url' => route('knowledge.show', ['knowledgeArticle' => $article->slug]),
                    'link_label' => 'Read the guide',
                ]);
            }
        }

        foreach ($businesses as $business) {
            $image = $this->imageUrl($business->cover_image ?: $business->logo);

            if ($image) {
                $slides->push([
                    'image' => $image,
                    'alt' => $business->name,
                    'label' => 'Business directory',
                    'title' => $business->name,
                    'description' => Str::limit($business->description, 130),
                    'url' => route('businesses.show', $business),
                    'link_label' => 'Meet the business',
                ]);
            }
        }

        return $slides->take(6)->values();
    }

    private function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }
}
