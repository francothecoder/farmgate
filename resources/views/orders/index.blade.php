@extends('layouts.app')

@section('title', 'Order tracking | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">FarmGate transactions</p>
            <h1 class="mt-3 text-3xl font-bold">Order tracking</h1>
            <p class="mt-2 max-w-2xl text-emerald-50/90">Follow agreed orders from confirmation to handoff. FarmGate records progress; buyers and sellers arrange payment and logistics directly.</p>
        </div>

        <div class="grid gap-5">
            @forelse($orders as $order)
                @php($isSeller = auth()->id() === $order->seller_id)
                @php($readyStatus = $order->fulfillment_method === 'delivery' ? 'delivered' : 'ready_for_pickup')
                @php($nextSellerStatus = match ([$order->status, $order->fulfillment_method]) {
                    ['confirmed', 'collection'], ['confirmed', 'delivery'] => 'preparing',
                    ['preparing', 'collection'] => 'ready_for_pickup',
                    ['preparing', 'delivery'] => 'dispatched',
                    ['dispatched', 'delivery'] => 'delivered',
                    default => null,
                })
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 p-5 sm:p-6">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Order #{{ $order->id }} · {{ $order->created_at->format('M j, Y') }}</p>
                            <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $order->offer?->request?->title ?? 'Buyer request' }}</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $isSeller ? 'Buyer' : 'Seller' }}: {{ $isSeller ? ($order->buyer?->full_name ?? $order->buyer?->name ?? 'FarmGate member') : ($order->seller?->full_name ?? $order->seller?->name ?? 'FarmGate member') }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-900">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                    </div>

                    <div class="grid gap-5 p-5 sm:p-6 lg:grid-cols-[.85fr_1.15fr]">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs text-slate-500">Agreed quantity</p>
                                    <p class="mt-1 font-bold text-slate-900">{{ number_format((float) $order->quantity, 2) }} {{ $order->unit }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs text-slate-500">Agreed total</p>
                                    <p class="mt-1 font-bold text-slate-900">{{ $order->currency }} {{ number_format((float) $order->total_price, 2) }}</p>
                                </div>
                            </div>
                            <div class="rounded-xl border border-slate-100 p-4 text-sm">
                                <p><span class="font-semibold text-slate-700">Fulfillment:</span> {{ $order->fulfillment_method === 'delivery' ? 'Seller delivery' : 'Buyer collection' }}</p>
                                @if($order->fulfillment_location)
                                    <p class="mt-2"><span class="font-semibold text-slate-700">Location:</span> {{ $order->fulfillment_location }}</p>
                                @endif
                                @if($order->fulfillment_notes)
                                    <p class="mt-2 whitespace-pre-line text-slate-600">{{ $order->fulfillment_notes }}</p>
                                @endif
                            </div>

                            @if($isSeller && $nextSellerStatus)
                                <form method="POST" action="{{ route('orders.update', $order) }}" class="grid gap-3 rounded-xl bg-emerald-50/60 p-4">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $nextSellerStatus }}">
                                    <label for="order-note-{{ $order->id }}" class="text-sm font-semibold text-slate-700">Update the buyer</label>
                                    <textarea id="order-note-{{ $order->id }}" name="note" rows="2" maxlength="1000" placeholder="Optional progress note or handoff details" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"></textarea>
                                    <button class="w-fit rounded-xl bg-[#14532D] px-4 py-2.5 text-sm font-semibold text-white">
                                        @if($nextSellerStatus === 'preparing') Start preparing
                                        @elseif($nextSellerStatus === 'ready_for_pickup') Mark ready for collection
                                        @elseif($nextSellerStatus === 'dispatched') Mark dispatched
                                        @else Confirm delivery
                                        @endif
                                    </button>
                                </form>
                            @endif

                            @if(! $isSeller && $order->status === $readyStatus)
                                <form method="POST" action="{{ route('orders.complete', $order) }}" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                    @csrf
                                    <p class="text-sm text-emerald-950">{{ $order->fulfillment_method === 'delivery' ? 'The seller marked this order delivered.' : 'The seller marked this order ready for collection.' }} Confirm when you have received it.</p>
                                    <button class="mt-3 rounded-xl bg-[#14532D] px-4 py-2.5 text-sm font-semibold text-white">Confirm receipt & complete</button>
                                </form>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700">Order timeline</h3>
                            <ol class="mt-4 grid gap-4">
                                @foreach($order->updates as $update)
                                    <li class="relative border-l-2 border-emerald-200 pl-4">
                                        <span class="absolute -left-[7px] top-1 h-3 w-3 rounded-full bg-emerald-600 ring-4 ring-white"></span>
                                        <p class="font-semibold text-slate-900">{{ str_replace('_', ' ', ucfirst($update->status)) }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $update->user?->full_name ?? $update->user?->name ?? 'FarmGate system' }} · {{ $update->created_at->format('M j, Y g:i A') }}</p>
                                        @if($update->note)
                                            <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $update->note }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <h2 class="text-xl font-bold text-slate-900">No orders to track yet</h2>
                    <p class="mt-2 text-sm text-slate-600">When you accept a supplier offer, FarmGate creates an order and keeps its progress here.</p>
                    <a href="{{ route('inbox.index') }}" class="mt-5 inline-flex rounded-full bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Go to your inbox</a>
                </div>
            @endforelse
        </div>
        @if($orders->hasPages())
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </section>
@endsection
