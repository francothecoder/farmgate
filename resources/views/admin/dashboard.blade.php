@extends('layouts.app')

@section('title', 'Admin Dashboard | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Administration</p>
            <h1 class="mt-3 text-3xl font-bold">FarmGate operations overview</h1>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Total users</p><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $totalUsers }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Active listings</p><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $activeListings }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Pending approvals</p><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $pendingApprovals }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Reports</p><p class="mt-3 text-3xl font-bold text-[#14532D]">{{ $reports }}</p></div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([
                'listings' => 'Review listings',
                'buyer-requests' => 'Review buyer requests',
                'businesses' => 'Review businesses',
                'reports' => 'Resolve reports',
            ] as $queue => $label)
                <a href="{{ route('admin.moderation.index', ['type' => $queue]) }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-[#16A34A]">
                    <span class="font-semibold text-slate-800">{{ $label }}</span>
                    <span class="text-[#14532D]">→</span>
                </a>
            @endforeach
            <a href="{{ route('admin.articles.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 font-semibold text-slate-800 shadow-sm hover:border-[#16A34A]">Manage knowledge articles</a>
            <a href="{{ route('admin.knowledge-comments.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 font-semibold text-slate-800 shadow-sm hover:border-[#16A34A]">Moderate knowledge discussions</a>
            <a href="{{ route('admin.settings.edit') }}" class="rounded-2xl border border-slate-200 bg-white p-5 font-semibold text-slate-800 shadow-sm hover:border-[#16A34A]">Edit site settings</a>
        </div>
    </section>
@endsection
