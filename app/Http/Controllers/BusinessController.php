<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(Request $request): View
    {
        $businesses = collect();

        if (Schema::hasTable('businesses')) {
            $query = Business::query()->where('status', 'approved');

            if ($request->filled('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            $businesses = $query->latest()->paginate(12);
        }

        return view('businesses.index', compact('businesses'));
    }

    public function show(Business $business): View
    {
        abort_unless($business->status === 'approved', 404);

        $business->load([
            'user' => fn ($query) => $query->withAvg('sellerReviews', 'rating')->withCount('sellerReviews'),
        ]);

        return view('businesses.show', compact('business'));
    }
}
