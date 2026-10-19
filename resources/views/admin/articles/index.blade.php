@extends('layouts.app')

@section('title', 'Knowledge CMS | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Administration / content</p>
                <h1 class="mt-3 text-3xl font-bold">Knowledge articles</h1>
            </div>
            <a href="{{ route('admin.articles.create') }}" class="rounded-xl bg-white px-4 py-3 text-sm font-semibold text-[#14532D]">Create article</a>
        </div>

        <form method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row">
            <input name="search" value="{{ request('search') }}" placeholder="Search by article title" class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">All editorial statuses</option>
                @foreach($statuses as $availableStatus)
                    <option value="{{ $availableStatus }}" @selected(request('status') === $availableStatus)>{{ str_replace('_', ' ', ucfirst($availableStatus)) }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Filter</button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse($articles as $article)
                    <a href="{{ route('admin.articles.edit', $article) }}" class="flex flex-col gap-3 p-5 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-semibold text-slate-900">{{ $article->title }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $article->category?->name }} · {{ $article->author?->name }} · Updated {{ $article->updated_at->diffForHumans() }}</p>
                        </div>
                        <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ str_replace('_', ' ', ucfirst($article->status)) }}</span>
                    </a>
                @empty
                    <p class="p-8 text-center text-sm text-slate-500">No articles match these filters.</p>
                @endforelse
            </div>
            <div class="border-t border-slate-100 p-4">{{ $articles->links() }}</div>
        </div>
    </section>
@endsection
