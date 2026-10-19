@extends('layouts.app')

@section('title', 'Knowledge Hub | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Knowledge hub</p>
            <h1 class="mt-3 text-3xl font-bold">Agricultural education for resilient farms</h1>
            <p class="mt-2 max-w-2xl text-emerald-50/90">Practical field guides and community conversations to help your farm thrive.</p>
        </div>

        <form method="GET" action="{{ route('knowledge.index') }}" class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_16rem_auto_auto] sm:items-end">
            <div>
                <label for="search" class="text-sm font-semibold text-slate-700">Search guides</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Soil, poultry, markets..." class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="category" class="text-sm font-semibold text-slate-700">Topic</label>
                <select id="category" name="category" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">All topics</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }} ({{ $category->articles_count }})</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-[#14532D] px-5 py-2.5 text-sm font-semibold text-white">Find guides</button>
            <a href="{{ route('knowledge.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-center text-sm font-semibold text-slate-700">Clear</a>
        </form>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($articles as $article)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <img src="{{ $article->featured_image_url ?? 'https://images.unsplash.com/photo-1464226184884-fa52ac9c3c74?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $article->title }}" class="h-48 w-full object-cover" />
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#16A34A]">{{ $article->category?->name }}</p>
                        <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $article->title }}</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ Str::limit($article->summary, 120) }}</p>
                        <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $article->reading_time ?? 5 }} min read</span>
                            <a href="{{ route('knowledge.show', ['knowledgeArticle' => $article->slug]) }}" class="font-semibold text-[#14532D]">Read article</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        @if($articles->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">No guides match those filters. Try another search or topic.</div>
        @endif
        <div class="mt-6">{{ $articles->links() }}</div>
    </section>
@endsection
