@extends('layouts.app')

@section('title', 'Knowledge discussions | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Administration / community</p>
                <h1 class="mt-3 text-3xl font-bold">Knowledge discussions</h1>
                <p class="mt-2 text-emerald-50/90">Review posts before they become visible to the community.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-white underline underline-offset-4">Back to overview</a>
        </div>

        <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div>
                <label for="status" class="text-sm font-medium text-slate-700">Comment status</label>
                <select id="status" name="status" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    @foreach(['pending', 'approved', 'rejected'] as $availableStatus)
                        <option value="{{ $availableStatus }}" @selected($status === $availableStatus)>{{ ucfirst($availableStatus) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Filter</button>
        </form>

        <div class="grid gap-4">
            @forelse($comments as $comment)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $comment->article?->title ?? 'Removed article' }}</p>
                            <p class="mt-1 text-sm text-slate-500">By {{ $comment->user?->full_name ?? $comment->user?->name ?? 'Former member' }} · {{ $comment->created_at->diffForHumans() }}</p>
                            @if($comment->parent)
                                <p class="mt-2 text-xs text-slate-500">Replying to {{ $comment->parent->user?->full_name ?? $comment->parent->user?->name ?? 'a member' }}</p>
                            @endif
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">{{ ucfirst($comment->status) }}</span>
                    </div>
                    <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $comment->body }}</p>
                    @if($status === 'pending' || $status === 'rejected' || $status === 'approved')
                        <div class="mt-4 flex gap-2">
                            @if($status === 'pending' || $status === 'rejected')
                                <form method="POST" action="{{ route('admin.knowledge-comments.update', $comment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button class="rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Approve</button>
                                </form>
                            @endif
                            @if($status === 'pending' || $status === 'approved')
                                <form method="POST" action="{{ route('admin.knowledge-comments.update', $comment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-800">Reject</button>
                                </form>
                            @endif
                        </div>
                    @endif
                    @if($comment->reviewed_at)
                        <p class="mt-3 text-xs text-slate-500">Reviewed {{ $comment->reviewed_at->diffForHumans() }}</p>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">No {{ $status }} discussion comments.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $comments->links() }}</div>
    </section>
@endsection
