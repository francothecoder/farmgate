@extends('layouts.app')

@section('title', 'Knowledge Hub | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Knowledge hub</p>
            <h1 class="mt-3 text-3xl font-bold">Agricultural education for resilient farms</h1>
        </div>

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
    </section>
@endsection
