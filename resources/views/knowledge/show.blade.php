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

        <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">FarmGate community</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Discussion</h2>
                    <p class="mt-1 text-sm text-slate-600">Ask questions, share field experience, and reply to other farmers. New posts are reviewed before they appear.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">{{ $comments->total() }} approved</span>
            </div>

            @auth
                <form method="POST" action="{{ route('knowledge.comments.store', $knowledgeArticle) }}" class="mt-6 grid gap-3">
                    @csrf
                    <label for="discussion-body" class="text-sm font-semibold text-slate-700">Start a discussion</label>
                    <textarea id="discussion-body" name="body" rows="3" maxlength="2000" required placeholder="Share a question or practical experience..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('body') }}</textarea>
                    @error('body')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                    <button class="w-fit rounded-xl bg-[#14532D] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0f3c26]">Post for review</button>
                </form>
            @else
                <p class="mt-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-700"><a class="font-semibold text-[#14532D] underline" href="{{ route('login') }}">Sign in</a> to join the discussion.</p>
            @endauth

            <div class="mt-8 grid gap-5">
                @forelse($comments as $comment)
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-semibold text-slate-900">{{ $comment->user?->full_name ?? $comment->user?->name ?? 'FarmGate member' }}</p>
                            <time class="text-xs text-slate-500" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $comment->body }}</p>

                        @if($comment->replies->isNotEmpty())
                            <div class="mt-5 grid gap-3 border-l-2 border-emerald-300 pl-4">
                                @foreach($comment->replies as $reply)
                                    <div class="rounded-xl bg-white p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="text-sm font-semibold text-slate-900">{{ $reply->user?->full_name ?? $reply->user?->name ?? 'FarmGate member' }}</p>
                                            <time class="text-xs text-slate-500" datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->diffForHumans() }}</time>
                                        </div>
                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $reply->body }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @auth
                            <details class="mt-4">
                                <summary class="w-fit cursor-pointer text-sm font-semibold text-[#14532D]">Reply to this discussion</summary>
                                <form method="POST" action="{{ route('knowledge.comments.store', $knowledgeArticle) }}" class="mt-3 grid gap-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <label for="reply-{{ $comment->id }}" class="sr-only">Reply to {{ $comment->user?->name }}</label>
                                    <textarea id="reply-{{ $comment->id }}" name="body" rows="2" maxlength="2000" required placeholder="Add a helpful reply..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"></textarea>
                                    <button class="w-fit rounded-xl border border-emerald-800 px-4 py-2 text-sm font-semibold text-emerald-900">Reply for review</button>
                                </form>
                            </details>
                        @endauth
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-600">No approved discussions yet. Start the first conversation.</div>
                @endforelse
            </div>
            @if($comments->hasPages())
                <div class="mt-5">{{ $comments->links() }}</div>
            @endif
        </section>
    </section>
@endsection
