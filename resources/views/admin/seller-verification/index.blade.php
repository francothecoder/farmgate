@extends('layouts.app')

@section('title', 'Seller verification | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Administration / trust</p>
                <h1 class="mt-3 text-3xl font-bold">Seller verification</h1>
                <p class="mt-2 max-w-2xl text-emerald-50/90">Verify sellers only after reviewing their active, approved FarmGate business or products. Verification reflects platform checks, not a guarantee of a transaction.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-white underline underline-offset-4">Back to overview</a>
        </div>

        <div class="grid gap-4">
            @forelse($sellers as $seller)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-bold text-slate-900">{{ $seller->full_name ?? $seller->name }}</h2>
                                @if($seller->seller_verified_at)
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Verified seller</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Not verified</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $seller->email }} · Joined {{ $seller->created_at->format('M Y') }}</p>
                            <p class="mt-2 text-sm text-slate-600">{{ $seller->approved_listings_count }} active approved {{ \Illuminate\Support\Str::plural('listing', $seller->approved_listings_count) }} · {{ $seller->approved_businesses_count }} approved {{ \Illuminate\Support\Str::plural('business', $seller->approved_businesses_count) }}</p>

                            @if($seller->businesses->isNotEmpty())
                                <p class="mt-3 text-sm font-semibold text-slate-700">Businesses:</p>
                                <ul class="mt-1 list-inside list-disc text-sm text-slate-600">
                                    @foreach($seller->businesses as $business)
                                        <li>{{ $business->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if($seller->listings->isNotEmpty())
                                <p class="mt-3 text-sm font-semibold text-slate-700">Approved products:</p>
                                <ul class="mt-1 list-inside list-disc text-sm text-slate-600">
                                    @foreach($seller->listings as $listing)
                                        <li>{{ $listing->title }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if($seller->seller_verified_at)
                                <p class="mt-3 text-xs text-slate-500">Verified {{ $seller->seller_verified_at->diffForHumans() }} by {{ $seller->sellerVerifier?->name ?? 'FarmGate admin' }}</p>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.seller-verification.update', $seller) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="{{ $seller->seller_verified_at ? 'revoke' : 'verify' }}">
                            <button class="rounded-xl {{ $seller->seller_verified_at ? 'border border-rose-200 bg-rose-50 text-rose-800' : 'bg-[#14532D] text-white' }} px-4 py-2.5 text-sm font-semibold">
                                {{ $seller->seller_verified_at ? 'Revoke verification' : 'Verify seller' }}
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">No sellers have approved listings or businesses to review yet.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $sellers->links() }}</div>
    </section>
@endsection
