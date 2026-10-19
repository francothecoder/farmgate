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
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        @if($business->user?->seller_verified_at)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800"><span aria-hidden="true">✓</span> Verified seller</span>
                        @endif
                        @if($business->user?->seller_reviews_count)
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
                                {{ number_format((float) $business->user->seller_reviews_avg_rating, 1) }} / 5 · {{ $business->user->seller_reviews_count }} buyer {{ \Illuminate\Support\Str::plural('review', $business->user->seller_reviews_count) }}
                            </span>
                        @endif
                    </div>
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

            @auth
                @if(auth()->id() !== $business->user_id)
                    <details class="mt-6 rounded-xl border border-rose-100 bg-rose-50/50 p-4">
                        <summary class="cursor-pointer text-sm font-semibold text-rose-800">Report this business</summary>
                        <form method="POST" action="{{ route('reports.store') }}" class="mt-3 grid gap-3">
                            @csrf
                            <input type="hidden" name="target_type" value="business">
                            <input type="hidden" name="target_id" value="{{ $business->id }}">
                            <label for="business-report-reason" class="text-sm font-medium text-slate-700">What is the issue?</label>
                            <select id="business-report-reason" name="reason" required class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                <option value="">Select a reason</option>
                                <option value="misleading">Misleading business details</option>
                                <option value="unavailable">Business appears inactive</option>
                                <option value="scam">Suspected scam</option>
                                <option value="inappropriate">Inappropriate content</option>
                                <option value="other">Other concern</option>
                            </select>
                            <textarea name="details" rows="2" maxlength="2000" placeholder="Add details to help our moderation team (optional)" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"></textarea>
                            @error('reason')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                            @error('target_id')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                            <button class="w-fit rounded-xl border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-800">Submit report</button>
                        </form>
                    </details>
                @endif
            @else
                <a href="{{ route('login') }}" class="mt-6 inline-flex text-sm font-semibold text-slate-500 underline">Sign in to report a concern</a>
            @endauth
        </div>
    </section>
@endsection
