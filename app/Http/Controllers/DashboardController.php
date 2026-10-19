<?php

namespace App\Http\Controllers;

use App\Models\BuyerRequest;
use App\Models\Business;
use App\Models\Enquiry;
use App\Models\Listing;
use App\Models\SavedArticle;
use App\Models\SavedListing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        $listings = Schema::hasTable('listings') ? Listing::where('user_id', $user->id)->latest()->limit(5)->get() : collect();
        $requests = Schema::hasTable('buyer_requests') ? BuyerRequest::where('user_id', $user->id)->latest()->limit(5)->get() : collect();
        $savedListings = Schema::hasTable('saved_listings') ? SavedListing::with('listing')->where('user_id', $user->id)->limit(5)->get() : collect();
        $savedArticles = Schema::hasTable('saved_articles') ? SavedArticle::with('article')->where('user_id', $user->id)->limit(5)->get() : collect();
        $businesses = Schema::hasTable('businesses') ? Business::where('user_id', $user->id)->latest()->limit(5)->get() : collect();
        $inquiries = Schema::hasTable('enquiries') ? Enquiry::where('recipient_id', $user->id)->orWhere('user_id', $user->id)->latest()->limit(5)->get() : collect();

        return view('dashboard', compact('listings', 'requests', 'savedListings', 'savedArticles', 'businesses', 'inquiries'));
    }
}
