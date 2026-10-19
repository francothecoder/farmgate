<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BuyerOffer;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerTrustTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_verify_an_eligible_seller_and_badge_is_shown_on_listing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create();
        $listing = $this->approvedListing($seller);

        $this->actingAs($admin)->patch(route('admin.seller-verification.update', $seller), [
            'action' => 'verify',
        ])->assertRedirect()
            ->assertSessionHas('success', $seller->name.' is now a FarmGate verified seller.');

        $this->assertDatabaseHas('users', [
            'id' => $seller->id,
            'seller_verified_by' => $admin->id,
        ]);
        $this->get(route('admin.seller-verification.index'))
            ->assertOk()
            ->assertSee($listing->title)
            ->assertSee('Verified seller');
        $this->get(route('listings.show', $listing))
            ->assertOk()
            ->assertSee('Verified seller');
    }

    public function test_non_admin_cannot_verify_a_seller_and_admin_cannot_verify_without_approved_activity(): void
    {
        $member = User::factory()->create();
        $seller = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($member)->get(route('admin.seller-verification.index'))->assertForbidden();
        $this->actingAs($member)->patch(route('admin.seller-verification.update', $seller), [
            'action' => 'verify',
        ])->assertForbidden();
        $this->actingAs($admin)->patch(route('admin.seller-verification.update', $seller), [
            'action' => 'verify',
        ])->assertNotFound();

        $this->assertNull($seller->fresh()->seller_verified_at);
    }

    public function test_buyer_can_review_a_supplier_only_after_accepting_an_offer(): void
    {
        [$buyer, $supplier, $offer] = $this->acceptedOffer();

        $this->actingAs($buyer)->post(route('inbox.offers.review', $offer), [
            'rating' => 5,
            'body' => 'The supplier provided clear details and responded promptly.',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Your seller review has been published.');

        $this->assertDatabaseHas('seller_reviews', [
            'buyer_offer_id' => $offer->id,
            'seller_id' => $supplier->id,
            'reviewer_id' => $buyer->id,
            'rating' => 5,
            'body' => 'The supplier provided clear details and responded promptly.',
        ]);
        $this->get(route('listings.show', $this->approvedListing($supplier)))
            ->assertOk()
            ->assertSee('5.0 / 5')
            ->assertSee('1 buyer review');
    }

    public function test_only_the_buyer_can_review_an_accepted_offer_once(): void
    {
        [$buyer, $supplier, $offer] = $this->acceptedOffer();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)->post(route('inbox.offers.review', $offer), [
            'rating' => 4,
        ])->assertForbidden();

        $this->actingAs($buyer)->post(route('inbox.offers.review', $offer), [
            'rating' => 4,
        ])->assertRedirect();

        $this->post(route('inbox.offers.review', $offer), [
            'rating' => 3,
        ])->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('seller_reviews', 1);
        $this->assertDatabaseHas('seller_reviews', [
            'buyer_offer_id' => $offer->id,
            'seller_id' => $supplier->id,
            'rating' => 4,
        ]);
    }

    public function test_review_is_refused_for_an_offer_that_has_not_been_accepted(): void
    {
        [$buyer, , $offer] = $this->acceptedOffer('submitted');

        $this->actingAs($buyer)->post(route('inbox.offers.review', $offer), [
            'rating' => 5,
        ])->assertStatus(409);

        $this->assertDatabaseCount('seller_reviews', 0);
    }

    public function test_member_can_report_an_approved_listing_once_for_admin_moderation(): void
    {
        $reporter = User::factory()->create();
        $seller = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $listing = $this->approvedListing($seller);

        $this->actingAs($reporter)->post(route('reports.store'), [
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'reason' => 'misleading',
            'details' => 'The advertised amount does not match the seller response.',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Thanks for helping keep FarmGate safe. Our team will review your report.');

        $report = Report::firstOrFail();
        $this->assertSame(Listing::class, $report->reportable_type);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'user_id' => $reporter->id,
            'reportable_id' => $listing->id,
            'reason' => 'misleading',
            'status' => 'pending',
        ]);
        $this->actingAs($admin)->get(route('admin.moderation.index', 'reports'))
            ->assertOk()
            ->assertSee('misleading')
            ->assertSee('The advertised amount does not match the seller response.');

        $this->actingAs($reporter)->post(route('reports.store'), [
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'reason' => 'scam',
        ])->assertSessionHasErrors('target_id');
        $this->assertDatabaseCount('reports', 1);
    }

    public function test_seller_cannot_report_their_own_listing_or_forge_a_report_target_type(): void
    {
        $seller = User::factory()->create();
        $listing = $this->approvedListing($seller);

        $this->actingAs($seller)->post(route('reports.store'), [
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'reason' => 'other',
        ])->assertForbidden();

        $this->post(route('reports.store'), [
            'target_type' => User::class,
            'target_id' => $seller->id,
            'reason' => 'other',
        ])->assertSessionHasErrors('target_type');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_buyer_can_report_an_approved_business_and_see_its_seller_trust_status(): void
    {
        $seller = User::factory()->create();
        $seller->forceFill(['seller_verified_at' => now()])->save();
        $buyer = User::factory()->create();
        $business = Business::create([
            'user_id' => $seller->id,
            'name' => 'Trusted Farm Inputs',
            'business_type' => 'Agricultural inputs',
            'description' => 'Farm supplies.',
            'status' => 'approved',
        ]);

        $this->actingAs($buyer)->get(route('businesses.show', $business))
            ->assertOk()
            ->assertSee('Verified seller')
            ->assertSee('Report this business');

        $this->post(route('reports.store'), [
            'target_type' => 'business',
            'target_id' => $business->id,
            'reason' => 'other',
            'details' => 'The listed address could not be confirmed.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'user_id' => $buyer->id,
            'reportable_type' => Business::class,
            'reportable_id' => $business->id,
            'reason' => 'other',
        ]);
    }

    private function approvedListing(User $seller): Listing
    {
        $category = Category::firstOrCreate(
            ['slug' => 'crops'],
            ['name' => 'Crops'],
        );

        return Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Quality maize '.$seller->id,
            'slug' => 'quality-maize-'.$seller->id,
            'description' => 'Dry maize from the latest harvest.',
            'status' => 'approved',
            'is_active' => true,
        ]);
    }

    /**
     * @return array{User, User, BuyerOffer}
     */
    private function acceptedOffer(string $status = 'accepted'): array
    {
        $buyer = User::factory()->create();
        $supplier = User::factory()->create();
        $category = Category::firstOrCreate(
            ['slug' => 'crops'],
            ['name' => 'Crops'],
        );
        $buyerRequest = BuyerRequest::create([
            'user_id' => $buyer->id,
            'category_id' => $category->id,
            'title' => 'Bulk maize needed '.$buyer->id,
            'slug' => 'bulk-maize-needed-'.$buyer->id,
            'description' => 'Looking for dry maize.',
            'status' => 'approved',
        ]);
        $offer = BuyerOffer::create([
            'buyer_request_id' => $buyerRequest->id,
            'user_id' => $supplier->id,
            'quantity' => 50,
            'price' => 25000,
            'status' => $status,
        ]);

        return [$buyer, $supplier, $offer];
    }
}
