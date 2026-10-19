@extends('layouts.app')

@section('title', 'Business Directory | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Business directory</p>
            <h1 class="mt-3 text-3xl font-bold">Trusted agricultural businesses in Zambia</h1>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($businesses as $business)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DCFCE7] text-xl">🏢</div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $business->name }}</h2>
                            <p class="text-xs text-slate-500">{{ $business->business_type }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">{{ Str::limit($business->description, 140) }}</p>
                    <div class="mt-4 text-sm text-slate-600">
                        <p>{{ $business->display_address }}</p>
                        <p>{{ $business->phone }}</p>
                    </div>
                    <a href="{{ route('businesses.show', $business) }}" class="mt-5 inline-block rounded-full bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">View profile</a>
                </article>
            @endforeach
        </div>
    </section>
@endsection
