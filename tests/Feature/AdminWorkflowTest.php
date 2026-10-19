<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesUploadedImages;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use CreatesUploadedImages;
    use RefreshDatabase;

    public function test_non_admin_cannot_open_moderation_or_cms(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.moderation.index', 'listings'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.articles.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_admin_can_open_moderation_queues_and_cms_forms(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.moderation.index', 'listings'))
            ->assertOk()
            ->assertSee('Listings');
        $this->get(route('admin.articles.create'))->assertOk()->assertSee('Article body');
        $this->get(route('admin.settings.edit'))->assertOk()->assertSee('Site settings');
    }

    public function test_admin_can_approve_a_listing_and_the_action_is_audited(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'New season maize',
            'slug' => 'new-season-maize',
            'description' => 'Quality maize.',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($admin)->from(route('admin.moderation.index', 'listings'))
            ->patch(route('admin.moderation.update', ['type' => 'listings', 'id' => $listing->id]), [
                'action' => 'approve',
            ]);

        $response->assertRedirect(route('admin.moderation.index', 'listings'));
        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'status' => 'approved']);
        $this->assertDatabaseHas('moderation_events', [
            'subject_type' => Listing::class,
            'subject_id' => $listing->id,
            'user_id' => $admin->id,
            'action' => 'approve',
        ]);
        $this->get(route('listings.show', ['listing' => $listing->slug]))->assertOk();
    }

    public function test_admin_must_give_a_reason_when_rejecting_a_listing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Unclear maize listing',
            'slug' => 'unclear-maize-listing',
            'description' => 'Needs more information.',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($admin)->from(route('admin.moderation.index', 'listings'))
            ->patch(route('admin.moderation.update', ['type' => 'listings', 'id' => $listing->id]), [
                'action' => 'reject',
            ]);

        $response->assertRedirect(route('admin.moderation.index', 'listings'))
            ->assertSessionHasErrors('reason');
        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'status' => 'pending_review']);
        $this->assertDatabaseCount('moderation_events', 0);
    }

    public function test_admin_can_reject_a_listing_with_a_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Listing with unclear price',
            'slug' => 'listing-with-unclear-price',
            'description' => 'Needs a clear price.',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.moderation.update', ['type' => 'listings', 'id' => $listing->id]), [
            'action' => 'reject',
            'reason' => 'Add a verifiable unit price.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'status' => 'rejected',
            'rejection_reason' => 'Add a verifiable unit price.',
        ]);
        $this->assertDatabaseHas('moderation_events', [
            'subject_type' => Listing::class,
            'subject_id' => $listing->id,
            'action' => 'reject',
            'reason' => 'Add a verifiable unit price.',
        ]);
    }

    public function test_admin_can_approve_business_and_buyer_request_and_resolve_a_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $business = Business::create([
            'user_id' => $owner->id,
            'name' => 'Lusaka Farm Supplies',
            'business_type' => 'Inputs',
            'description' => 'Agricultural inputs.',
            'status' => 'pending_review',
        ]);
        $buyerRequest = BuyerRequest::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Need maize',
            'slug' => 'need-maize',
            'description' => 'Buying dry maize.',
            'status' => 'pending_review',
        ]);
        $report = Report::create([
            'user_id' => $owner->id,
            'reportable_type' => Listing::class,
            'reportable_id' => 1,
            'reason' => 'Incorrect offer',
        ]);

        $this->actingAs($admin)->patch(route('admin.moderation.update', ['type' => 'businesses', 'id' => $business->id]), [
            'action' => 'approve',
        ])->assertRedirect();
        $this->patch(route('admin.moderation.update', ['type' => 'buyer-requests', 'id' => $buyerRequest->id]), [
            'action' => 'approve',
        ])->assertRedirect();
        $this->patch(route('admin.moderation.update', ['type' => 'reports', 'id' => $report->id]), [
            'action' => 'resolve',
        ])->assertRedirect();

        $this->assertDatabaseHas('businesses', ['id' => $business->id, 'status' => 'approved']);
        $this->assertNotNull($business->fresh()->verified_at);
        $this->assertDatabaseHas('buyer_requests', ['id' => $buyerRequest->id, 'status' => 'approved']);
        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'resolved']);
        $this->assertDatabaseCount('moderation_events', 3);
    }

    public function test_admin_can_publish_an_article_with_a_cover_and_unsafe_html_is_not_rendered(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = KnowledgeCategory::create(['name' => 'Crop Production', 'slug' => 'crop-production']);

        $response = $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Soil care for maize',
            'category_id' => $category->id,
            'summary' => 'Practical guidance.',
            'body' => "## Healthy soil\n\n**Build organic matter.** <script>alert(1)</script>\n\n[Unsafe link](javascript:alert(1))",
            'status' => 'published',
            'reading_time' => 4,
            'seo_title' => 'Healthy soil guidance',
            'seo_description' => 'Learn ways to protect soil and improve maize yields.',
            'tags' => 'soil, maize',
            'is_featured' => '1',
            'author_id' => 999,
            'reviewer_id' => 999,
            'featured_image' => $this->fakePng('soil.png', 400, 200),
        ]);

        $article = KnowledgeArticle::where('title', 'Soil care for maize')->firstOrFail();
        $response->assertRedirect(route('admin.articles.edit', $article));
        $this->assertDatabaseHas('knowledge_articles', [
            'id' => $article->id,
            'author_id' => $admin->id,
            'reviewer_id' => $admin->id,
            'status' => 'published',
            'slug' => 'soil-care-for-maize',
        ]);
        Storage::disk('public')->assertExists($article->featured_image);
        $this->assertDatabaseHas('moderation_events', [
            'subject_type' => KnowledgeArticle::class,
            'subject_id' => $article->id,
            'action' => 'publish',
        ]);

        $this->get(route('knowledge.show', ['knowledgeArticle' => $article->slug]))
            ->assertOk()
            ->assertSee('Build organic matter.')
            ->assertSee('Learn ways to protect soil and improve maize yields.')
            ->assertDontSee('<script>')
            ->assertDontSee('href="javascript:');
    }

    public function test_admin_can_update_site_settings_and_public_layout_uses_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'FarmGate Central',
            'tagline' => 'Built for Zambian agriculture.',
            'contact_email' => 'support@farmgate.example',
            'contact_phone' => '+260 977 123456',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => 'FarmGate Central',
        ]);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('FarmGate Central')
            ->assertSee('support@farmgate.example');
    }

    public function test_business_and_article_pages_hide_unapproved_records_from_the_public(): void
    {
        $user = User::factory()->create();
        $category = KnowledgeCategory::create(['name' => 'Crop Production', 'slug' => 'crop-production']);
        $article = KnowledgeArticle::create([
            'category_id' => $category->id,
            'title' => 'Draft article',
            'slug' => 'draft-article',
            'body' => 'Not published.',
            'author_id' => $user->id,
            'status' => 'draft',
        ]);
        $business = Business::create([
            'user_id' => $user->id,
            'name' => 'Pending business',
            'business_type' => 'Inputs',
            'description' => 'Pending approval.',
            'status' => 'pending_review',
        ]);

        $this->get(route('knowledge.show', $article))->assertNotFound();
        $this->get(route('businesses.show', $business))->assertNotFound();
    }
}
