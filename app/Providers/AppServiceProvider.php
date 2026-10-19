<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function (ViewInstance $view): void {
            $settings = Schema::hasTable('site_settings')
                ? SiteSetting::whereIn('key', ['site_name', 'tagline', 'contact_email', 'contact_phone'])->pluck('value', 'key')
                : collect();

            $view->with('siteSettings', $settings);
        });
    }
}
