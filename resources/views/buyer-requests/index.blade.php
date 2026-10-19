@extends('layouts.app')

@section('title', 'Buyer Requests | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Buyer requests</p>
            <h1 class="mt-3 text-3xl font-bold">Trusted sourcing requests from active buyers</h1>
        </div>

        @if($requests->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">No approved buyer requests yet.</div>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($requests as $request)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#16A34A]">{{ $request->category?->name }}</p>
                        <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $request->title }}</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ $request->quantity }} {{ $request->unit }} · {{ $request->location }}</p>
                        <p class="mt-3 text-sm text-slate-700">{{ Str::limit($request->description, 120) }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="font-semibold text-[#14532D]">Target: ZMW {{ number_format((float) $request->target_price, 0) }}</span>
                            <a href="{{ route('buyer-requests.show', ['buyerRequest' => $request->slug]) }}" class="rounded-full bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">View</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
