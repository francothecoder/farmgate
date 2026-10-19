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

                <div class="flex flex-col gap-3">
                    @if(auth()->id() !== $listing->user_id && $listing->user?->whatsapp_url)
                        <a href="{{ $listing->user->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#25D366] px-5 py-3 font-semibold text-white transition hover:bg-[#1fb85a] sm:w-auto">
                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M20.52 3.48A11.86 11.86 0 0 0 12.08 0C5.53 0 .2 5.32.2 11.87c0 2.1.55 4.15 1.6 5.95L.1 24l6.34-1.66a11.86 11.86 0 0 0 5.64 1.43h.01c6.55 0 11.88-5.33 11.88-11.88 0-3.17-1.24-6.15-3.45-8.41ZM12.09 21.75a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.76.99 1-3.67-.24-.38a9.86 9.86 0 0 1-1.52-5.23c0-5.47 4.45-9.92 9.92-9.92a9.83 9.83 0 0 1 7.02 2.91 9.85 9.85 0 0 1 2.9 7.02c0 5.47-4.45 9.92-9.92 9.92Zm5.44-7.43c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.46-.88-.78-1.47-1.74-1.64-2.04-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.28.3-1.05 1.02-1.05 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.2 5.08 4.49.71.31 1.27.5 1.7.64.72.23 1.37.2 1.89.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35Z"/></svg>
                            Chat with seller on WhatsApp
                            <span aria-hidden="true">↗</span>
                        </a>
                    @endif
                    @auth
                        @if(auth()->id() !== $listing->user_id)
                            <div class="w-full space-y-4">
                                <form method="POST" action="{{ route('enquiries.store') }}" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="related_type" value="listing">
                                    <input type="hidden" name="related_id" value="{{ $listing->id }}">
                                    <label for="seller-message" class="block text-sm font-semibold text-slate-800">Message the seller</label>
                                    <textarea id="seller-message" name="message" rows="3" required maxlength="1000" class="w-full rounded-xl border border-slate-200 p-3" placeholder="Ask about availability, delivery, or quality"></textarea>
                                    @error('message')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                                    <button type="submit" class="rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Send enquiry</button>
                                    <a href="{{ route('inbox.index') }}" class="ml-2 text-sm font-semibold text-[#14532D]">Track replies in inbox</a>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('listings.create') }}" class="rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Post another listing</a>
                        @endif
                    @else
                        @if(!$listing->user?->whatsapp_url)
                            <a href="{{ route('login') }}" class="rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Login to enquire</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection
