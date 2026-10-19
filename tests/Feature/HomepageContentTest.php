<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\KnowledgeCategory;
use App\Models\Listing;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\SampleContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_selects_products_that_appear_in_homepage_slider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $featured = $this->listing($seller, $category, 'slider-maize', 'Slider maize');
        $notFeatured = $this->listing($seller, $category, 'other-beans', 'Other beans');

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'FarmGate',
            'tagline' => 'Agriculture together.',
            'contact_email' => 'hello@example.com',
            'contact_phone' => '+260 900 000000',
            'home_slider_listing_ids' => [$featured->id],
        ])->assertRedirect();

        $this->assertSame(
            json_encode([$featured->id]),
            SiteSetting::where('key', 'home_slider_listing_ids')->value('value'),
        );
        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('homeSlides', fn ($slides) => $slides->pluck('title')->all() === ['Slider maize']);
    }

    public function test_admin_cannot_select_a_pending_product_for_homepage_slider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $pending = $this->listing($seller, $category, 'pending-maize', 'Pending maize', 'pending_review');

        $this->actingAs($admin)->from(route('admin.settings.edit'))
            ->put(route('admin.settings.update'), [
                'site_name' => 'FarmGate',
                'tagline' => 'Agriculture together.',
                'contact_email' => 'hello@example.com',
                'contact_phone' => '+260 900 000000',
                'home_slider_listing_ids' => [$pending->id],
            ])->assertSessionHasErrors('home_slider_listing_ids');

        $this->assertDatabaseMissing('site_settings', ['key' => 'home_slider_listing_ids']);
    }

    public function test_sample_content_seeder_creates_at_least_ten_approved_products_and_published_guides(): void
    {
        $this->seed(SampleContentSeeder::class);

        $this->assertDatabaseCount('listings', 20);
        $this->assertDatabaseCount('knowledge_articles', 10);
    }

    public function test_knowledge_hub_lists_topics_and_filters_guides_by_category(): void
    {
        $this->seed(SampleContentSeeder::class);
        $livestock = KnowledgeCategory::where('slug', 'livestock')->firstOrFail();

        $this->get(route('knowledge.index'))
            ->assertOk()
            ->assertSee('Search guides')
            ->assertSee('Livestock');

        $this->get(route('knowledge.index', ['category' => $livestock->id]))
            ->assertOk()
            ->assertSee('Smallholder poultry: a practical starter guide')
            ->assertDontSee('Prepare produce for a stronger market');
    }

    private function listing(User $seller, Category $category, string $slug, string $title, string $status = 'approved'): Listing
    {
        return Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'description' => 'A sample agricultural product.',
            'featured_image' => 'https://example.com/product.jpg',
            'status' => $status,
            'is_active' => true,
        ]);
    }
}
