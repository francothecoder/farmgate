<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $sellers = User::query()
            ->where('role', '!=', 'admin')
            ->where(fn ($query) => $query
                ->whereHas('listings', fn ($listings) => $listings->where('status', 'approved')->where('is_active', true))
                ->orWhereHas('businesses', fn ($businesses) => $businesses->where('status', 'approved')))
            ->withCount([
                'listings as approved_listings_count' => fn ($listings) => $listings->where('status', 'approved')->where('is_active', true),
                'businesses as approved_businesses_count' => fn ($businesses) => $businesses->where('status', 'approved'),
            ])
            ->with([
                'sellerVerifier',
                'listings' => fn ($listings) => $listings
                    ->where('status', 'approved')
                    ->where('is_active', true)
                    ->latest()
                    ->limit(3),
                'businesses' => fn ($businesses) => $businesses
                    ->where('status', 'approved')
                    ->latest()
                    ->limit(3),
            ])
            ->latest()
            ->paginate(20);

        return view('admin.seller-verification.index', compact('sellers'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        abort_if($user->role === 'admin', 403);
        abort_unless(
            $user->listings()->where('status', 'approved')->where('is_active', true)->exists()
            || $user->businesses()->where('status', 'approved')->exists(),
            404,
        );

        $validated = $request->validate([
            'action' => ['required', 'in:verify,revoke'],
        ]);

        $user->forceFill([
            'seller_verified_at' => $validated['action'] === 'verify' ? now() : null,
            'seller_verified_by' => $validated['action'] === 'verify' ? $request->user()->id : null,
        ])->save();

        return back()->with('success', $validated['action'] === 'verify'
            ? $user->name.' is now a FarmGate verified seller.'
            : $user->name.' seller verification has been revoked.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
