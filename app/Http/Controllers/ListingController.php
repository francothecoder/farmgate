<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $listings = collect();

        if (Schema::hasTable('listings')) {
            $query = Listing::with(['category', 'user', 'images'])
                ->where('status', 'approved')
                ->where('is_active', true);

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%'.$request->search.'%')
                        ->orWhere('description', 'like', '%'.$request->search.'%');
                });
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('province')) {
                $query->where('province_id', $request->province);
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            $listings = $query->latest()->paginate(12);
        }

        return view('marketplace.index', compact('listings'));
    }

    public function show(Listing $listing): View
    {
        abort_unless($listing->status === 'approved' && $listing->is_active, 404);

        $listing->load(['category', 'user', 'images', 'country', 'province', 'district']);

        return view('marketplace.show', compact('listing'));
    }
}
