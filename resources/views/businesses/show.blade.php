@extends('layouts.app')

@section('title', $business->name.' | Business Directory')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#DCFCE7] text-3xl">🏢</div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">{{ $business->business_type }}</p>
                    <h1 class="mt-1 text-3xl font-bold text-slate-900">{{ $business->name }}</h1>
                </div>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">About</h2>
                    <p class="mt-3 text-slate-700">{{ $business->description }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-700">
                    <p><strong>Phone:</strong> {{ $business->phone }}</p>
                    <p class="mt-2"><strong>Email:</strong> {{ $business->email }}</p>
                    <p class="mt-2"><strong>Website:</strong> {{ $business->website }}</p>
                    <p class="mt-2"><strong>Location:</strong> {{ $business->display_address }}</p>
                </div>
            </div>

            @auth
                @if(auth()->id() !== $business->user_id)
                    <form method="POST" action="{{ route('enquiries.store') }}" class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5">
                        @csrf
                        <input type="hidden" name="related_type" value="business">
                        <input type="hidden" name="related_id" value="{{ $business->id }}">
                        <label for="business-message" class="font-semibold text-slate-900">Contact this business</label>
                        <p class="mt-1 text-sm text-slate-600">Ask about products, services, or how to work together.</p>
                        <textarea id="business-message" name="message" rows="3" required maxlength="1000" class="mt-3 w-full rounded-xl border border-slate-200 p-3" placeholder="Write a message"></textarea>
                        @error('message')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        <button class="mt-3 rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Send business enquiry</button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="mt-8 inline-flex rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Login to contact this business</a>
            @endauth
        </div>
    </section>
@endsection
