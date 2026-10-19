<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    private const SETTING_KEYS = ['site_name', 'tagline', 'contact_email', 'contact_phone'];

    public function edit(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.settings.edit', [
            'settings' => SiteSetting::whereIn('key', self::SETTING_KEYS)->pluck('value', 'key'),
            'featuredListings' => Listing::where('status', 'approved')
                ->where('is_active', true)
                ->whereNotNull('featured_image')
                ->orderBy('title')
                ->get(['id', 'title', 'featured_image']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'tagline' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'home_slider_listing_ids' => ['sometimes', 'array', 'max:6'],
            'home_slider_listing_ids.*' => ['required', 'integer', 'distinct', 'exists:listings,id'],
        ]);

        $sliderListingIds = array_map('intval', $validated['home_slider_listing_ids'] ?? []);

        if (count($sliderListingIds) !== Listing::where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('featured_image')
            ->whereIn('id', $sliderListingIds)
            ->count()) {
            throw ValidationException::withMessages([
                'home_slider_listing_ids' => 'Choose only active, approved products with a featured image.',
            ]);
        }

        foreach ($validated as $key => $value) {
            if ($key === 'home_slider_listing_ids') {
                continue;
            }

            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group_name' => 'general'],
            );
        }

        SiteSetting::updateOrCreate(
            ['key' => 'home_slider_listing_ids'],
            ['value' => json_encode($sliderListingIds), 'group_name' => 'homepage'],
        );

        return back()->with('success', 'Site settings updated.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
