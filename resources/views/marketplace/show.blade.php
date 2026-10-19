@extends('layouts.app')

@section('title', $listing->title.' | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.1fr_.9fr]">
            <div>
                <img src="{{ $listing->featured_image_url ?? 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $listing->title }}" class="h-[420px] w-full rounded-3xl object-cover shadow-lg" />
                <div class="mt-5 grid grid-cols-3 gap-3">
                    @foreach($listing->images->take(3) as $image)
                        <img src="{{ $image->url }}" alt="{{ $listing->title }}" class="h-28 w-full rounded-2xl object-cover" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="rounded-full bg-[#DCFCE7] px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#14532D]">{{ $listing->category?->name }}</span>
                    <span class="text-sm font-medium text-slate-500">{{ $listing->status }}</span>
                </div>
                <h1 class="text-3xl font-bold text-slate-900">{{ $listing->title }}</h1>
                <div class="flex items-center gap-4 text-sm text-slate-600">
                    <span>{{ $listing->quantity }} {{ $listing->unit }}</span>
                    <span>•</span>
                    <span>{{ $listing->province?->name ?? 'Zambia' }}</span>
                </div>
                <p class="text-4xl font-bold text-[#14532D]">ZMW {{ number_format($listing->price, 0) }}</p>
                <p class="text-slate-700">{{ $listing->description }}</p>

                <div class="grid gap-3 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                    <div class="flex justify-between"><span>Condition</span><span>{{ $listing->condition ?? 'Good' }}</span></div>
                    <div class="flex justify-between"><span>Availability</span><span>{{ ucfirst($listing->availability) }}</span></div>
                    <div class="flex justify-between"><span>Location</span><span>{{ $listing->location_detail ?? 'Regional delivery available' }}</span></div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    @auth
                        <form method="POST" action="{{ route('enquiries.store') }}">
                            @csrf
                            <input type="hidden" name="recipient_id" value="{{ $listing->user_id }}">
                            <input type="hidden" name="subject" value="Enquiry about {{ $listing->title }}">
                            <input type="hidden" name="related_type" value="listing">
                            <input type="hidden" name="related_id" value="{{ $listing->id }}">
                            <textarea name="message" rows="3" class="w-full rounded-xl border border-slate-200 p-3" placeholder="Send a direct enquiry to the seller"></textarea>
                            <button type="submit" class="mt-3 rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Contact seller</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Login to enquire</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection
