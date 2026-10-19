<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\KnowledgeArticle;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        return view('admin.dashboard', [
            'totalUsers' => Schema::hasTable('users') ? User::count() : 0,
            'activeListings' => Schema::hasTable('listings') ? Listing::where('status', 'approved')->count() : 0,
            'pendingListings' => Schema::hasTable('listings') ? Listing::where('status', 'pending_review')->count() : 0,
            'buyerRequests' => Schema::hasTable('buyer_requests') ? BuyerRequest::count() : 0,
            'businessProfiles' => Schema::hasTable('businesses') ? Business::count() : 0,
            'publishedArticles' => Schema::hasTable('knowledge_articles') ? KnowledgeArticle::where('status', 'published')->count() : 0,
            'pendingApprovals' => (
                (Schema::hasTable('listings') ? Listing::where('status', 'pending_review')->count() : 0)
                + (Schema::hasTable('businesses') ? Business::where('status', 'pending_review')->count() : 0)
                + (Schema::hasTable('buyer_requests') ? BuyerRequest::where('status', 'pending_review')->count() : 0)
            ),
            'reports' => Schema::hasTable('reports') ? Report::where('status', 'pending')->count() : 0,
            'verifiedSellers' => Schema::hasTable('users') ? User::whereNotNull('seller_verified_at')->count() : 0,
        ]);
    }
}
