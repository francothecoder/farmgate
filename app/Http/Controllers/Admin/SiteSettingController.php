<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    private const SETTING_KEYS = ['site_name', 'tagline', 'contact_email', 'contact_phone'];

    public function edit(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.settings.edit', [
            'settings' => SiteSetting::whereIn('key', self::SETTING_KEYS)->pluck('value', 'key'),
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
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group_name' => 'general'],
            );
        }

        return back()->with('success', 'Site settings updated.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
