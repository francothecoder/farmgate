@extends('layouts.app')

@section('title', $buyerRequest->title.' | Buyer Requests')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#16A34A]">{{ $buyerRequest->category?->name }}</p>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">{{ $buyerRequest->title }}</h1>
            <p class="mt-3 text-slate-700">{{ $buyerRequest->quantity }} {{ $buyerRequest->unit }} · {{ $buyerRequest->location }}</p>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Target price</p><p class="mt-2 text-xl font-bold text-[#14532D]">ZMW {{ number_format((float) $buyerRequest->target_price, 0) }}</p></div>
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Required date</p><p class="mt-2 text-xl font-bold text-slate-900">{{ $buyerRequest->required_date ?? 'Flexible' }}</p></div>
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Status</p><p class="mt-2 text-xl font-bold text-slate-900">{{ $buyerRequest->status }}</p></div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_.9fr]">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Request details</h2>
                    <p class="mt-3 text-slate-700">{{ $buyerRequest->description }}</p>
                    <p class="mt-6 text-sm font-medium text-slate-700">Quality requirements: {{ $buyerRequest->quality_requirements }}</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h3 class="text-lg font-bold text-slate-900">Submit an offer</h3>
                    @auth
                        <form method="POST" action="{{ route('buyer-requests.offers.store', $buyerRequest) }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="number" name="quantity" placeholder="Offered quantity" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
                            <input type="number" name="price" placeholder="Offered price" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
                            <input type="date" name="availability_date" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                            <textarea name="message" rows="4" placeholder="Message for the buyer" class="w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
                            <button type="submit" class="w-full rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Send offer</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-block rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Login to submit an offer</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection
