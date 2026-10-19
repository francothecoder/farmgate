<?php

namespace Tests\Feature;

use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_listings_cover_multiple_categories_and_seed_idempotently(): void
    {
        $this->seed();
        $this->seed();

        $listings = Listing::whereIn('slug', [
            'maize-grain-for-sale',
            'vegetable-seedling-bundle',
            'fresh-soybeans-central-province',
            'sun-dried-groundnuts',
            'fresh-tomatoes-lusaka',
            'organic-free-range-eggs',
            'sunflower-seed-bulk',
            'fresh-honey-northern-province',
            'dried-common-beans',
            'fresh-sweet-potatoes',
            'crisp-cabbage-lusaka',
            'ripe-mangoes-eastern-province',
            'goats-for-smallholder-farms',
            'point-of-lay-chickens',
            'composted-organic-manure',
            'manual-maize-sheller',
            'drip-irrigation-starter-kit',
            'layer-poultry-feed',
            'soil-testing-farm-service',
            'dried-sorghum-grain',
        ])->with('category')->get();

        $this->assertCount(20, $listings);
        $this->assertGreaterThanOrEqual(8, $listings->pluck('category.slug')->unique()->count());
        $this->assertSame(20, Listing::count());
        $this->assertTrue($listings->every(
            fn (Listing $listing): bool => $listing->status === 'approved' && $listing->is_active,
        ));
    }
}
