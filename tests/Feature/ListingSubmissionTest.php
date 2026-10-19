<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesUploadedImages;
use Tests\TestCase;

class ListingSubmissionTest extends TestCase
{
    use CreatesUploadedImages;
    use RefreshDatabase;

    public function test_verified_seller_can_submit_a_listing_with_multiple_images_for_review(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);

        $response = $this->actingAs($seller)->post(route('listings.store'), [
            'title' => 'Fresh maize',
            'description' => 'Dry maize harvested this season.',
            'category_id' => $category->id,
            'quantity' => 20,
            'unit' => 'bags',
            'price' => 400,
            'negotiable' => '1',
            'user_id' => 999,
            'status' => 'approved',
            'is_active' => false,
            'images' => [
                $this->fakePng('maize-cover.png', 300, 300),
                $this->fakePng('maize-second.png', 300, 300),
            ],
        ]);

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Your listing has been submitted for review.');

        $listing = Listing::where('title', 'Fresh maize')->firstOrFail();
        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'user_id' => $seller->id,
            'status' => 'pending_review',
            'is_active' => true,
            'featured_image' => $listing->images()->first()->path,
        ]);
        $this->assertDatabaseCount('listing_images', 2);
        Storage::disk('public')->assertExists($listing->images()->first()->path);
        Storage::disk('public')->assertExists($listing->images()->latest()->first()->path);
    }

    public function test_seller_cannot_upload_a_non_image_file_as_a_listing_photo(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Crops', 'slug' => 'crops']);

        $response = $this->actingAs($seller)->from('/listings/create')->post(route('listings.store'), [
            'title' => 'Fresh maize',
            'description' => 'Dry maize harvested this season.',
            'category_id' => $category->id,
            'quantity' => 20,
            'unit' => 'bags',
            'price' => 400,
            'images' => [UploadedFile::fake()->create('payload.jpg', 5, 'text/plain')],
        ]);

        $response->assertRedirect('/listings/create')
            ->assertSessionHasErrors('images.0');
        $this->assertDatabaseMissing('listings', ['title' => 'Fresh maize']);
        $this->assertDatabaseCount('listing_images', 0);
    }
}
