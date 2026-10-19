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
        </div>
    </section>
@endsection
