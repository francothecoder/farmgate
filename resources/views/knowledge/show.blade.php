@extends('layouts.app')

@section('title', ($knowledgeArticle->seo_title ?: $knowledgeArticle->title).' | Knowledge Hub')
@section('meta_description', $knowledgeArticle->seo_description ?: $knowledgeArticle->summary)
@section('meta_keywords', $knowledgeArticle->tags)

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <img src="{{ $knowledgeArticle->featured_image_url ?? 'https://images.unsplash.com/photo-1464226184884-fa52ac9c3c74?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $knowledgeArticle->title }}" class="h-80 w-full rounded-2xl object-cover" />
            <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-[#16A34A]">{{ $knowledgeArticle->category?->name }}</p>
            <h1 class="mt-3 text-4xl font-bold text-slate-900">{{ $knowledgeArticle->title }}</h1>
            <p class="mt-3 text-sm text-slate-500">By {{ $knowledgeArticle->author?->name }} · {{ $knowledgeArticle->reading_time ?? 5 }} minute read</p>
            <div class="prose prose-slate mt-8 max-w-none">{!! $knowledgeArticle->rendered_body !!}</div>
        </article>
    </section>
@endsection
