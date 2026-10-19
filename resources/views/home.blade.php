@extends('layouts.app')

@section('title', 'FarmGate Zambia | Everything Agriculture, One Place.')

@section('content')
    @if($homeSlides->isNotEmpty())
        <section class="bg-[#f0f7f1] px-3 pb-8 pt-5 sm:px-6 lg:px-8">
            <div
                x-data="homeImageSlider(@js($homeSlides))"
                x-on:mouseenter="stopTimer()"
                x-on:mouseleave="startTimer()"
                x-on:focusin="stopTimer()"
                x-on:focusout="if (!$el.contains($event.relatedTarget)) startTimer()"
                x-on:keydown.left.prevent="previous()"
                x-on:keydown.right.prevent="next()"
                aria-label="Featured FarmGate stories"
                aria-roledescription="carousel"
                tabindex="0"
                class="relative mx-auto min-h-[29rem] max-w-[88rem] overflow-hidden rounded-[2rem] bg-[#123b28] shadow-xl shadow-emerald-950/15 outline-none ring-1 ring-emerald-950/10 focus-visible:ring-2 focus-visible:ring-orange-400 sm:min-h-[34rem] lg:min-h-[39rem]"
            >
                <img
                    x-bind:src="slides[activeIndex].image"
                    x-bind:alt="slides[activeIndex].alt"
                    x-bind:key="activeIndex"
                    x-transition.opacity.duration.500ms
                    class="absolute inset-0 h-full w-full object-cover"
                    fetchpriority="high"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#082b1b]/95 via-[#082b1b]/75 to-[#082b1b]/30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#082b1b]/70 via-transparent to-transparent"></div>
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-orange-500/15 to-transparent"></div>

                <div class="absolute inset-0 flex flex-col justify-between p-6 text-white sm:p-10 lg:p-14">
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-[#103d2a]/55 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur">
                            <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                            <span x-text="slides[activeIndex].label"></span>
                        </span>
                        <span class="rounded-full border border-white/20 bg-black/20 px-3 py-2 text-xs font-semibold text-white/90 backdrop-blur">
                            <span x-text="String(activeIndex + 1).padStart(2, '0')"></span>
                            <span class="px-1 text-orange-300">/</span>
                            <span x-text="String(slides.length).padStart(2, '0')"></span>
                        </span>
                    </div>

                    <div class="max-w-2xl">
                        <p class="mb-4 text-sm font-semibold uppercase tracking-[0.24em] text-orange-300">Growing opportunity across Zambia</p>
                        <h2 class="max-w-2xl text-4xl font-extrabold leading-tight tracking-tight drop-shadow sm:text-5xl lg:text-6xl" x-text="slides[activeIndex].title"></h2>
                        <p class="mt-5 max-w-xl text-base leading-7 text-white/85 sm:text-lg" x-text="slides[activeIndex].description"></p>
                        <a
                            x-bind:href="slides[activeIndex].url"
                            x-text="slides[activeIndex].link_label"
                            class="mt-7 inline-flex items-center gap-3 rounded-full bg-orange-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-950/25 transition hover:-translate-y-0.5 hover:bg-orange-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            <span aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div x-show="slides.length > 1" class="flex flex-wrap items-center justify-between gap-4 border-t border-white/20 pt-5">
                        <div class="flex items-center gap-2" aria-label="Choose a featured story">
                            <template x-for="(slide, index) in slides" x-bind:key="index">
                                <button
                                    type="button"
                                    x-on:click="show(index)"
                                    x-bind:aria-label="`Show story ${index + 1}: ${slide.title}`"
                                    x-bind:aria-current="activeIndex === index ? 'true' : 'false'"
                                    class="h-2.5 rounded-full ring-1 ring-white/80 transition-all"
                                    x-bind:class="activeIndex === index ? 'w-9 bg-orange-400 ring-orange-300' : 'w-2.5 bg-white/40 hover:bg-white/80'"
                                ></button>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" x-on:click="previous()" aria-label="Previous story" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/35 bg-white/5 text-xl transition hover:border-orange-300 hover:bg-orange-500">‹</button>
                            <button type="button" x-on:click="next()" aria-label="Next story" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/35 bg-white/5 text-xl transition hover:border-orange-300 hover:bg-orange-500">›</button>
                            <button
                                type="button"
                                x-on:click="isPlaying ? pause() : play()"
                                x-bind:aria-label="isPlaying ? 'Pause automatic slides' : 'Play automatic slides'"
                                x-text="isPlaying ? 'Pause' : 'Play'"
                                class="rounded-full border border-white/35 px-4 py-2.5 text-xs font-semibold transition hover:border-orange-300 hover:bg-orange-500"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="bg-[#f0f7f1] px-3 pb-8 pt-5 sm:px-6 lg:px-8">
            <div class="mx-auto flex min-h-[29rem] max-w-[88rem] flex-col justify-end rounded-[2rem] bg-gradient-to-br from-[#14532D] via-[#0c4028] to-[#16A34A] p-8 text-white shadow-xl sm:min-h-[34rem] sm:p-12">
                <span class="mb-5 inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em]"><span class="h-2 w-2 rounded-full bg-orange-400"></span> FarmGate Zambia</span>
                <h2 class="max-w-3xl text-4xl font-extrabold leading-tight sm:text-6xl">Where Zambia grows together.</h2>
                <p class="mt-5 max-w-xl text-lg text-emerald-50/90">Discover the products, people, and practical knowledge moving agriculture forward.</p>
            </div>
        </section>
    @endif

    <section class="relative overflow-hidden bg-gradient-to-br from-[#14532D] via-[#10452c] to-[#0b3926] text-white">
        <div class="absolute -right-24 -top-32 h-96 w-96 rounded-full bg-orange-400/10 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.15fr_.85fr] lg:items-center lg:px-8 lg:py-16">
            <div>
                <p class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-[0.2em] text-orange-300"><span class="h-px w-8 bg-orange-400"></span> The home of Zambian agriculture</p>
                <h1 class="mt-5 max-w-2xl text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">Everything agriculture.<br><span class="text-emerald-300">One thriving community.</span></h1>
                <p class="mt-5 max-w-xl text-lg leading-7 text-emerald-50/85">Find quality products, connect directly with buyers and suppliers, and grow with practical knowledge made for the field.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('listings.index') }}" class="rounded-full bg-orange-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-orange-950/20 transition hover:-translate-y-0.5 hover:bg-orange-400">Explore marketplace <span aria-hidden="true">↗</span></a>
                    <a href="{{ route('knowledge.index') }}" class="rounded-full border border-white/30 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:border-emerald-300 hover:bg-white/10">Learn & grow</a>
                </div>
            </div>

            <div class="rounded-3xl border border-white/15 bg-white/10 p-3 shadow-xl backdrop-blur">
                <form method="GET" action="{{ route('listings.index') }}" class="flex flex-col gap-3 rounded-2xl bg-white p-3 sm:flex-row">
                    <label for="home-search" class="sr-only">Search the FarmGate marketplace</label>
                    <input id="home-search" type="search" name="search" placeholder="What are you looking for?" class="min-w-0 flex-1 rounded-xl border-0 bg-[#f3f7f3] px-4 py-3.5 text-sm text-slate-800 outline-none ring-1 ring-transparent placeholder:text-slate-500 focus:ring-emerald-600">
                    <button type="submit" class="rounded-xl bg-[#14532D] px-6 py-3.5 text-sm font-bold text-white transition hover:bg-[#0f3c26]">Search FarmGate</button>
                </form>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 px-3 pb-2 pt-4 text-xs font-medium text-emerald-50/80">
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-orange-400"></span>Products & inputs</span>
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>Buyer opportunities</span>
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-lime-300"></span>Practical knowledge</span>
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
