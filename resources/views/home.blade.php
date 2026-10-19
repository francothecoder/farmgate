@extends('layouts.app')

@section('title', 'FarmGate Zambia | Everything Agriculture, One Place.')

@section('content')
    <section class="bg-gradient-to-br from-[#14532D] via-[#0b4226] to-[#16A34A] text-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div class="space-y-6">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-100">
                    Trusted agricultural network
                </span>
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Everything Agriculture, One Place.</h1>
                <p class="max-w-xl text-lg text-emerald-50/90">
                    Discover agricultural products, connect with buyers, and access practical farming knowledge across Zambia.
                </p>

                <form method="GET" action="{{ route('listings.index') }}" class="flex max-w-xl flex-col gap-3 rounded-2xl bg-white/10 p-3 backdrop-blur sm:flex-row">
                    <input type="search" name="search" placeholder="Search products, farms, knowledge..." class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-emerald-100/70 outline-none" />
                    <button type="submit" class="rounded-xl bg-[#22C55E] px-5 py-3 text-sm font-semibold text-[#14532D] hover:bg-[#4ade80]">Search</button>
                </form>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('listings.index') }}" class="rounded-full bg-[#22C55E] px-6 py-3 text-center text-sm font-semibold text-[#14532D] hover:bg-[#4ade80]">Explore Marketplace</a>
                    <a href="{{ route('knowledge.index') }}" class="rounded-full border border-white/30 px-6 py-3 text-center text-sm font-semibold text-white hover:bg-white/10">Learn Farming</a>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-3xl bg-white/10 p-5 backdrop-blur">
                    <p class="text-sm text-emerald-100">Active buyers</p>
                    <p class="mt-3 text-4xl font-bold">160+</p>
                    <p class="mt-2 text-sm text-emerald-100/80">Matching demand across crop and livestock categories.</p>
                </div>
                <div class="rounded-3xl bg-[#DCFCE7] p-5 text-[#14532D]">
                    <p class="text-sm">Verified sellers</p>
                    <p class="mt-3 text-4xl font-bold">89</p>
                    <p class="mt-2 text-sm text-[#14532D]/80">Trusted businesses and producers in this network.</p>
                </div>
                <div class="rounded-3xl bg-white/10 p-5 backdrop-blur sm:col-span-2">
                    <p class="text-sm text-emerald-100">Knowledge library</p>
                    <div class="mt-3 space-y-2">
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2 text-sm"><span>Crop production</span><span>34 guides</span></div>
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2 text-sm"><span>Soil & fertilizer</span><span>19 guides</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">Popular categories</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Browse agricultural segments</h2>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach($featuredCategories as $category)
                <a href="{{ route('listings.index', ['category' => $category->id]) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-[#16A34A] hover:shadow-md">
                    <div class="inline-flex rounded-xl bg-[#DCFCE7] p-3 text-[#14532D]">🌾</div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ $category->name }}</h3>
                    <p class="mt-2 text-sm text-slate-600">Fresh opportunities and trusted listing access.</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">New in the market</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">Recently approved listings</h2>
                </div>
                <a href="{{ route('listings.index') }}" class="text-sm font-semibold text-[#14532D] hover:text-[#0f3c26]">View all</a>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($featuredListings as $listing)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <img src="{{ $listing->featured_image ?? 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $listing->title }}" class="h-52 w-full object-cover" />
                        <div class="p-5">
                            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                                <span>{{ $listing->category?->name }}</span>
                                <span>{{ $listing->status }}</span>
                            </div>
                            <h3 class="mt-3 text-xl font-bold text-slate-900">{{ $listing->title }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ Str::limit($listing->description, 110) }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <p class="text-2xl font-bold text-[#14532D]">ZMW {{ number_format($listing->price, 0) }}</p>
                                <a href="{{ route('listings.show', ['listing' => $listing->slug]) }}" class="rounded-full bg-[#DCFCE7] px-3 py-2 text-sm font-semibold text-[#14532D]">View</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">Buyer needs</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Active buyer requests</h2>
            </div>
            <a href="{{ route('buyer-requests.index') }}" class="text-sm font-semibold text-[#14532D] hover:text-[#0f3c26]">View all</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($buyerRequests as $request)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#16A34A]">{{ $request->category?->name }}</p>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">{{ $request->title }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $request->quantity }} {{ $request->unit }} · {{ $request->location }}</p>
                    <p class="mt-4 text-sm font-semibold text-[#14532D]">Target price: ZMW {{ number_format((float) $request->target_price, 0) }}</p>
                    <a href="{{ route('buyer-requests.show', ['buyerRequest' => $request->slug]) }}" class="mt-5 inline-block rounded-full bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Review request</a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-slate-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">Farm insight</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">Featured agricultural knowledge</h2>
                </div>
                <a href="{{ route('knowledge.index') }}" class="text-sm font-semibold text-[#14532D] hover:text-[#0f3c26]">Explore library</a>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach($articles as $article)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <img src="{{ $article->featured_image_url ?? 'https://images.unsplash.com/photo-1464226184884-fa52ac9c3c74?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $article->title }}" class="h-40 w-full rounded-xl object-cover" />
                        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-[#16A34A]">{{ $article->category?->name }}</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $article->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ Str::limit($article->summary, 120) }}</p>
                        <a href="{{ route('knowledge.show', ['knowledgeArticle' => $article->slug]) }}" class="mt-4 inline-block text-sm font-semibold text-[#14532D]">Read article</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">Trusted buyers & sellers</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Featured businesses</h2>
            </div>
            <a href="{{ route('businesses.index') }}" class="text-sm font-semibold text-[#14532D] hover:text-[#0f3c26]">Browse directory</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach($businesses as $business)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DCFCE7] text-xl">🏢</div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $business->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $business->business_type }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">{{ Str::limit($business->description, 120) }}</p>
                    <a href="{{ route('businesses.show', $business) }}" class="mt-4 inline-block rounded-full bg-[#16A34A] px-4 py-2 text-sm font-semibold text-white">View profile</a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-[#14532D] py-12 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-200">Free listing</p>
                <h2 class="mt-2 text-3xl font-bold">List your farm produce or services today.</h2>
            </div>
            <a href="{{ route('register') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-[#14532D]">Open your account</a>
        </div>
    </section>
@endsection
