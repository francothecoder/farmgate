<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\KnowledgeArticle;
use App\Models\Listing;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredCategories = Schema::hasTable('categories') ? Category::query()->limit(6)->get() : collect();
        $featuredListings = Schema::hasTable('listings') ? Listing::with(['category', 'user'])
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

        return view('home', compact(
            'featuredCategories',
            'featuredListings',
            'buyerRequests',
            'articles',
            'businesses'
        ));
    }
}
