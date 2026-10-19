<?php

namespace Tests\Feature;

use App\Models\BuyerOffer;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\Enquiry;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConnectionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_enquire_about_a_listing_without_choosing_the_recipient(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Quality maize',
            'slug' => 'quality-maize',
            'description' => 'Dry maize.',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $response = $this->actingAs($buyer)->post(route('enquiries.store'), [
            'related_type' => 'listing',
            'related_id' => $listing->id,
            'recipient_id' => $buyer->id,
            'subject' => 'Forged subject',
            'message' => 'Can you deliver to Lusaka?',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Your message has been sent. You can follow replies from your inbox.');
        $this->assertDatabaseHas('enquiries', [
            'user_id' => $buyer->id,
            'recipient_id' => $seller->id,
            'related_type' => 'listing',
            'related_id' => $listing->id,
            'subject' => 'Enquiry about Quality maize',
            'message' => 'Can you deliver to Lusaka?',
        ]);
    }

    public function test_users_cannot_enquire_about_their_own_or_unapproved_listings(): void
    {
        $owner = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $listing = Listing::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Private draft',
            'slug' => 'private-draft',
            'description' => 'Not approved.',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $this->actingAs($owner)->post(route('enquiries.store'), [
            'related_type' => 'listing',
            'related_id' => $listing->id,
            'message' => 'I want to enquire about my listing.',
        ])->assertForbidden();

        $listing->update(['status' => 'pending_review']);
        $buyer = User::factory()->create();
        $this->actingAs($buyer)->post(route('enquiries.store'), [
            'related_type' => 'listing',
            'related_id' => $listing->id,
            'message' => 'Can I buy it?',
        ])->assertNotFound();

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_listing_detail_shows_a_whatsapp_link_for_a_registered_seller_phone(): void
    {
        $seller = User::factory()->create(['phone' => '+260 955 123 456']);
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Quality maize',
            'slug' => 'quality-maize',
            'description' => 'Dry maize.',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $this->get(route('listings.show', ['listing' => $listing->slug]))
            ->assertOk()
            ->assertSee('https://wa.me/260955123456')
            ->assertSee('Chat with seller on WhatsApp');

        $seller->update(['phone' => null]);

        $this->get(route('listings.show', ['listing' => $listing->slug]))
            ->assertOk()
            ->assertDontSee('https://wa.me/');
    }

    public function test_enquiry_recipient_can_reply_and_sender_can_see_the_reply_in_inbox(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $enquiry = Enquiry::create([
            'user_id' => $buyer->id,
            'recipient_id' => $seller->id,
            'subject' => 'About maize',
            'message' => 'Is delivery available?',
            'status' => 'new',
        ]);

        $this->actingAs($seller)->post(route('inbox.enquiries.reply', $enquiry), [
            'response_message' => 'Yes, delivery to Lusaka is available.',
        ])->assertRedirect();

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'status' => 'replied',
            'response_message' => 'Yes, delivery to Lusaka is available.',
        ]);
        $this->assertNotNull($enquiry->fresh()->responded_at);
        $this->actingAs($buyer)->get(route('inbox.index'))
            ->assertOk()
            ->assertSee('Yes, delivery to Lusaka is available.');
    }

    public function test_only_the_buyer_can_accept_a_supplier_offer(): void
    {
        $buyer = User::factory()->create();
        $supplier = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $buyerRequest = BuyerRequest::create([
            'user_id' => $buyer->id,
            'category_id' => $category->id,
            'title' => 'Bulk maize needed',
            'slug' => 'bulk-maize-needed',
            'description' => 'Need dry maize.',
            'status' => 'approved',
        ]);
        $offer = BuyerOffer::create([
            'buyer_request_id' => $buyerRequest->id,
            'user_id' => $supplier->id,
            'quantity' => 50,
            'price' => 25000,
            'message' => 'Available now.',
            'status' => 'submitted',
        ]);

        $this->actingAs($otherUser)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'accepted',
        ])->assertForbidden();
        $this->assertDatabaseHas('buyer_offers', ['id' => $offer->id, 'status' => 'submitted']);

        $this->actingAs($buyer)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'accepted',
            'response_message' => 'Please arrange collection on Friday.',
        ])->assertRedirect();

        $this->assertDatabaseHas('buyer_offers', [
            'id' => $offer->id,
            'status' => 'accepted',
            'response_message' => 'Please arrange collection on Friday.',
        ]);
        $this->assertNotNull($offer->fresh()->responded_at);
        $this->actingAs($supplier)->get(route('inbox.index'))
            ->assertOk()
            ->assertSee('Please arrange collection on Friday.');

        $this->actingAs($buyer)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'declined',
        ])->assertStatus(409);
        $this->assertDatabaseHas('buyer_offers', ['id' => $offer->id, 'status' => 'accepted']);
    }

    public function test_request_owner_cannot_submit_an_offer_to_their_own_request(): void
    {
        $buyer = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);
        $buyerRequest = BuyerRequest::create([
            'user_id' => $buyer->id,
            'category_id' => $category->id,
            'title' => 'Bulk maize needed',
            'slug' => 'bulk-maize-needed',
            'description' => 'Need dry maize.',
            'status' => 'approved',
        ]);

        $this->actingAs($buyer)->post(route('buyer-requests.offers.store', $buyerRequest), [
            'quantity' => 50,
            'price' => 25000,
        ])->assertForbidden();

        $this->assertDatabaseCount('buyer_offers', 0);
    }
}
