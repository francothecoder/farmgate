@extends('layouts.app')

@section('title', $title.' moderation | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Administration / moderation</p>
                <h1 class="mt-3 text-3xl font-bold">{{ $title }}</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-white underline underline-offset-4">Back to overview</a>
        </div>

        <nav class="mb-6 flex flex-wrap gap-2" aria-label="Moderation queues">
            @foreach(['listings' => 'Listings', 'buyer-requests' => 'Buyer requests', 'businesses' => 'Businesses', 'reports' => 'Reports'] as $queue => $label)
                <a href="{{ route('admin.moderation.index', ['type' => $queue]) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ $type === $queue ? 'bg-[#14532D] text-white' : 'border border-slate-200 bg-white text-slate-700' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <form method="GET" class="mb-6 flex items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div>
                <label for="status" class="text-sm font-medium text-slate-700">Queue status</label>
                <select id="status" name="status" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    @foreach($statuses as $availableStatus)
                        <option value="{{ $availableStatus }}" @selected($status === $availableStatus)>{{ str_replace('_', ' ', ucfirst($availableStatus)) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Filter</button>
        </form>

        <div class="grid gap-4">
            @forelse($items as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-bold text-slate-900">
                                    {{ $item->title ?? $item->name ?? 'Report #'.$item->id }}
                                </h2>
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">{{ str_replace('_', ' ', $item->status) }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">
                                Submitted by {{ $item->user?->full_name ?? $item->user?->name ?? 'Unknown user' }} · {{ $item->created_at->format('M j, Y') }}
                            </p>
                            @if($type === 'reports')
                                <p class="mt-3 text-sm font-medium text-slate-800">{{ $item->reason }}</p>
                                <p class="mt-1 text-xs text-slate-500">Reported {{ class_basename($item->reportable_type) }} #{{ $item->reportable_id }}</p>
                                @if($item->details)
                                    <p class="mt-1 text-sm text-slate-600">{{ $item->details }}</p>
                                @endif
                            @else
                                <p class="mt-3 line-clamp-3 text-sm text-slate-600">{{ $item->description ?? $item->summary ?? $item->business_type }}</p>
                                @if($item->category)
                                    <p class="mt-2 text-xs font-medium text-slate-500">{{ $item->category->name }}</p>
                                @endif
                            @endif
                            @if($item->moderationEvents->isNotEmpty())
                                <div class="mt-4 border-t border-slate-100 pt-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Decision history</p>
                                    <ul class="mt-2 grid gap-2">
                                        @foreach($item->moderationEvents->take(3) as $event)
                                            <li class="text-xs text-slate-600">
                                                {{ str_replace('_', ' ', ucfirst($event->action)) }} by {{ $event->user?->name ?? 'Former admin' }} · {{ $event->created_at->diffForHumans() }}
                                                @if($event->reason) — {{ $event->reason }}@endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.moderation.update', ['type' => $type, 'id' => $item->id]) }}" class="grid w-full gap-3 sm:grid-cols-[1fr_auto] lg:max-w-xl">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="reason-{{ $item->id }}" class="sr-only">Decision note</label>
                                <input id="reason-{{ $item->id }}" name="reason" value="{{ old('reason') }}" maxlength="2000" placeholder="Decision note (required for reject, suspend or dismiss)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if($type === 'reports')
                                    @foreach(['resolve' => 'Resolve', 'dismiss' => 'Dismiss'] as $action => $label)
                                        <button name="action" value="{{ $action }}" class="rounded-xl bg-[#14532D] px-3 py-2 text-sm font-semibold text-white">{{ $label }}</button>
                                    @endforeach
                                @else
                                    @foreach(['approve' => 'Approve', 'reject' => 'Reject'] as $action => $label)
                                        <button name="action" value="{{ $action }}" class="rounded-xl {{ $action === 'approve' ? 'bg-[#14532D] text-white' : 'border border-rose-200 bg-rose-50 text-rose-800' }} px-3 py-2 text-sm font-semibold">{{ $label }}</button>
                                    @endforeach
                                    @if($status === 'approved')
                                        <button name="action" value="suspend" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800">Suspend</button>
                                    @endif
                                @endif
                                @if(in_array($status, ['rejected', 'suspended', 'dismissed', 'resolved'], true))
                                    <button name="action" value="reopen" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">Reopen</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">There are no {{ strtolower($title) }} with this status.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $items->links() }}</div>
    </section>
@endsection
