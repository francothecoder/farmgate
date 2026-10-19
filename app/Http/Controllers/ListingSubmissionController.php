<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ListingSubmissionController extends Controller
{
    public function create(): View
    {
        return view('marketplace.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'negotiable' => ['sometimes', 'boolean'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'location_detail' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', 'string', 'max:100'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000'],
        ]);

        $storedPaths = [];

        try {
            $listing = DB::transaction(function () use ($request, $validated, &$storedPaths): Listing {
                $listing = Listing::create([
                    ...collect($validated)->except('images')->all(),
                    'user_id' => $request->user()->id,
                    'slug' => $this->uniqueSlug($validated['title']),
                    'currency' => 'ZMW',
                    'negotiable' => $request->boolean('negotiable'),
                    'availability' => 'available',
                    'status' => 'pending_review',
                    'is_active' => true,
                ]);

                foreach ($request->file('images', []) as $image) {
                    $path = $image->storePublicly('listing-images', 'public');

                    if (! is_string($path)) {
                        throw new \RuntimeException('A listing image could not be stored.');
                    }

                    $storedPaths[] = $path;
                    $listing->images()->create(['path' => $path]);
                }

                if ($storedPaths !== []) {
                    $listing->update(['featured_image' => $storedPaths[0]]);
                }

                return $listing;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your listing has been submitted for review.');
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'listing';
        $slug = $base;
        $suffix = 2;

        while (Listing::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
