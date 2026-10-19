<?php

namespace Tests\Feature;

use App\Models\BuyerOffer;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_an_offer_requires_a_delivery_location_when_delivery_is_selected(): void
    {
        [$buyer, , $offer] = $this->submittedOffer();

        $this->actingAs($buyer)->from(route('inbox.index'))
            ->patch(route('inbox.offers.respond', $offer), [
                'status' => 'accepted',
                'fulfillment_method' => 'delivery',
            ])->assertSessionHasErrors('fulfillment_location');

        $this->assertDatabaseHas('buyer_offers', ['id' => $offer->id, 'status' => 'submitted']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_collection_order_is_tracked_until_buyer_confirms_receipt(): void
    {
        [$buyer, $seller, $offer] = $this->submittedOffer();

        $this->actingAs($buyer)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'accepted',
            'fulfillment_method' => 'collection',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Offer accepted. Your order has been created and is ready to track.');

        $order = Order::where('buyer_offer_id', $offer->id)->firstOrFail();
        $this->assertSame('confirmed', $order->status);

        $this->actingAs($seller)->patch(route('orders.update', $order), [
            'status' => 'preparing',
            'note' => 'Sorting and packing your order.',
        ])->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'preparing']);

        $this->patch(route('orders.update', $order), [
            'status' => 'ready_for_pickup',
            'note' => 'Ready to collect at the farm gate.',
        ])->assertRedirect();
        $this->assertDatabaseHas('order_updates', [
            'order_id' => $order->id,
            'status' => 'ready_for_pickup',
            'note' => 'Ready to collect at the farm gate.',
            'user_id' => $seller->id,
        ]);

        $this->actingAs($buyer)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Order #'.$order->id)
            ->assertSee('Ready for pickup')
            ->assertSee('Confirm receipt');

        $this->post(route('orders.complete', $order))->assertRedirect()
            ->assertSessionHas('success', 'Order marked complete. You can now leave a review for the seller.');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
        $this->assertDatabaseHas('order_updates', [
            'order_id' => $order->id,
            'status' => 'completed',
            'user_id' => $buyer->id,
        ]);
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_delivery_order_requires_valid_seller_progress_before_buyer_completion(): void
    {
        [$buyer, $seller, $offer] = $this->submittedOffer();
        $this->actingAs($buyer)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'accepted',
            'fulfillment_method' => 'delivery',
            'fulfillment_location' => 'Plot 18, Great East Road, Lusaka',
        ])->assertRedirect();

        $order = Order::where('buyer_offer_id', $offer->id)->firstOrFail();
        $this->actingAs($buyer)->post(route('orders.complete', $order))->assertStatus(409);

        $this->actingAs($seller)->patch(route('orders.update', $order), [
            'status' => 'preparing',
        ])->assertRedirect();
        $this->patch(route('orders.update', $order), [
            'status' => 'dispatched',
            'note' => 'Driver has collected the order.',
        ])->assertRedirect();
        $this->patch(route('orders.update', $order), [
            'status' => 'delivered',
            'note' => 'Order delivered to the requested address.',
        ])->assertRedirect();

        $this->actingAs($buyer)->post(route('orders.complete', $order))->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'fulfillment_method' => 'delivery',
            'fulfillment_location' => 'Plot 18, Great East Road, Lusaka',
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('order_updates', [
            'order_id' => $order->id,
            'status' => 'delivered',
            'user_id' => $seller->id,
        ]);
    }

    public function test_only_order_participants_can_view_or_update_order_progress(): void
    {
        [$buyer, $seller, $offer] = $this->submittedOffer();
        $otherUser = User::factory()->create();
        $this->actingAs($buyer)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'accepted',
            'fulfillment_method' => 'collection',
        ])->assertRedirect();
        $order = Order::where('buyer_offer_id', $offer->id)->firstOrFail();

        $this->actingAs($otherUser)->get(route('orders.index'))
            ->assertOk()
            ->assertDontSee('Order #'.$order->id);
        $this->patch(route('orders.update', $order), ['status' => 'preparing'])->assertForbidden();
        $this->post(route('orders.complete', $order))->assertForbidden();

        $this->actingAs($seller)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Order #'.$order->id);
    }

    public function test_seller_cannot_skip_order_stages_or_update_an_order_as_buyer(): void
    {
        [$buyer, $seller, $offer] = $this->submittedOffer();
        $this->actingAs($buyer)->patch(route('inbox.offers.respond', $offer), [
            'status' => 'accepted',
            'fulfillment_method' => 'delivery',
            'fulfillment_location' => 'Lusaka',
        ])->assertRedirect();
        $order = Order::where('buyer_offer_id', $offer->id)->firstOrFail();

        $this->actingAs($seller)->patch(route('orders.update', $order), [
            'status' => 'delivered',
        ])->assertSessionHasErrors('status');

        $this->actingAs($buyer)->patch(route('orders.update', $order), [
            'status' => 'preparing',
        ])->assertForbidden();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);
        $this->assertDatabaseCount('order_updates', 1);
    }

    private function submittedOffer(): array
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $category = Category::firstOrCreate(['slug' => 'crops'], ['name' => 'Crops']);
        $request = BuyerRequest::create([
            'user_id' => $buyer->id,
            'category_id' => $category->id,
            'title' => 'Maize order '.$buyer->id,
            'slug' => 'maize-order-'.$buyer->id,
            'description' => 'Seeking quality maize.',
            'location' => 'Lusaka',
            'status' => 'approved',
        ]);
        $offer = BuyerOffer::create([
            'buyer_request_id' => $request->id,
            'user_id' => $seller->id,
            'quantity' => 50,
            'price' => 25000,
            'status' => 'submitted',
        ]);

        return [$buyer, $seller, $offer];
    }
}
