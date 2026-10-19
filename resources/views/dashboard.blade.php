@extends('layouts.app')

@section('title', 'Dashboard | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Dashboard</p>
            <h1 class="mt-3 text-3xl font-bold">Welcome back, {{ auth()->user()->name }}</h1>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('listings.create') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-[#14532D]">Create a listing</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold text-white">Admin console</a>
                @endif
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h3 class="text-lg font-bold">My listings</h3><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $listings->count() }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h3 class="text-lg font-bold">Buyer requests</h3><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $requests->count() }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h3 class="text-lg font-bold">Saved listings</h3><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $savedListings->count() }}</p></div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Recent listings</h2>
                <ul class="mt-4 space-y-3">
                    @foreach($listings as $listing)
                        <li class="flex items-center justify-between border-b border-slate-100 pb-2"><span>{{ $listing->title }}</span><span class="text-sm text-slate-500">{{ $listing->status }}</span></li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Latest enquiries</h2>
                <ul class="mt-4 space-y-3">
                    @foreach($inquiries as $enquiry)
                        <li class="border-b border-slate-100 pb-2"><p class="font-medium text-slate-900">{{ $enquiry->subject }}</p><p class="text-sm text-slate-500">{{ Str::limit($enquiry->message, 80) }}</p></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection
