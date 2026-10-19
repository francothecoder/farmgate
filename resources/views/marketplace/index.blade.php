@extends('layouts.app')

@section('title', 'Marketplace | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Marketplace</p>
            <h1 class="mt-3 text-3xl font-bold">Agricultural products and services</h1>
        </div>

        <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Filters</h2>
                <form method="GET" action="{{ route('listings.index') }}" class="mt-5 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Min price</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Max price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Apply filters</button>
                </form>
            </aside>

            <div>
                @if($listings->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">No listings available right now. Please check back soon.</div>
                @else
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($listings as $listing)
                            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                <img src="{{ $listing->featured_image_url ?? 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $listing->title }}" class="h-48 w-full object-cover" />
                                <div class="p-5">
                                    <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                                        <span>{{ $listing->category?->name }}</span>
                                        <span>{{ $listing->province?->name ?? 'Zambia' }}</span>
                                    </div>
                                    <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $listing->title }}</h2>
                                    <p class="mt-2 text-sm text-slate-600">{{ Str::limit($listing->description, 100) }}</p>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-xl font-bold text-[#14532D]">ZMW {{ number_format($listing->price, 0) }}</span>
                                        <a href="{{ route('listings.show', ['listing' => $listing->slug]) }}" class="rounded-full bg-[#DCFCE7] px-3 py-2 text-sm font-semibold text-[#14532D]">View</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
