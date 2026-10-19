<?php

namespace App\Http\Controllers;

use App\Models\BuyerOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerReviewController extends Controller
{
    public function store(Request $request, BuyerOffer $buyerOffer): RedirectResponse
    {
        abort_unless(
            $buyerOffer->request()->where('user_id', $request->user()->id)->exists(),
            403,
        );

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $buyerOffer, $validated): void {
            $offer = BuyerOffer::query()->lockForUpdate()->findOrFail($buyerOffer->id);

            abort_unless($offer->status === 'accepted', 409);

            if ($offer->sellerReview()->exists()) {
                throw ValidationException::withMessages([
                    'rating' => 'You have already reviewed this accepted offer.',
                ]);
            }

            $offer->sellerReview()->create([
                'seller_id' => $offer->user_id,
                'reviewer_id' => $request->user()->id,
                'rating' => $validated['rating'],
                'body' => $validated['body'] ?? null,
            ]);
        });

        return back()->with('success', 'Your seller review has been published.');
    }
}
