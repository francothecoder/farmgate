@extends('layouts.app')

@section('title', 'Your inbox | FarmGate Zambia')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#14532D] to-[#16A34A] p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Your FarmGate connections</p>
            <h1 class="mt-3 text-3xl font-bold">Inbox & offers</h1>
            <p class="mt-2 text-emerald-50/90">Reply to enquiries and decide on supplier offers from one place.</p>
        </div>

        <div class="grid gap-8">
            <section>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Respond to buyers</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Incoming enquiries</h2>
                    </div>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-sm font-bold text-orange-800">{{ $receivedEnquiries->total() }}</span>
                </div>
                <div class="grid gap-4">
                    @forelse($receivedEnquiries as $enquiry)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $enquiry->subject }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">From {{ $enquiry->sender?->full_name ?? $enquiry->sender?->name ?? 'FarmGate user' }} · {{ $enquiry->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $enquiry->responded_at ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800' }}">{{ $enquiry->responded_at ? 'Replied' : 'Needs reply' }}</span>
                            </div>
                            <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $enquiry->message }}</p>

                            @if($enquiry->response_message)
                                <div class="mt-4 rounded-xl border-l-4 border-emerald-500 bg-emerald-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-800">Your reply · {{ $enquiry->responded_at?->diffForHumans() }}</p>
                                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $enquiry->response_message }}</p>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('inbox.enquiries.reply', $enquiry) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto]">
                                @csrf
                                <label for="enquiry-reply-{{ $enquiry->id }}" class="sr-only">Reply to {{ $enquiry->sender?->name }}</label>
                                <textarea id="enquiry-reply-{{ $enquiry->id }}" name="response_message" rows="2" maxlength="1000" required placeholder="Reply with availability, pricing, or next steps" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('response_message') }}</textarea>
                                @error('response_message')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                                <button class="rounded-xl bg-[#14532D] px-5 py-3 text-sm font-semibold text-white">Send reply</button>
                            </form>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No enquiries yet. Keep your listings and business profile up to date so buyers can find you.</div>
                    @endforelse
                </div>
                <div class="mt-4">{{ $receivedEnquiries->links() }}</div>
            </section>

            <section>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Make a decision</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Supplier offers on your requests</h2>
                    </div>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-sm font-bold text-orange-800">{{ $receivedOffers->total() }}</span>
                </div>
                <div class="grid gap-4">
                    @forelse($receivedOffers as $offer)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#16A34A]">{{ $offer->request?->title }}</p>
                                    <h3 class="mt-1 font-bold text-slate-900">Offer from {{ $offer->user?->full_name ?? $offer->user?->name ?? 'Supplier' }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $offer->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($offer->status) }}</span>
                            </div>
                            <div class="mt-4 grid gap-3 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-3">
                                <p><span class="text-slate-500">Quantity:</span> <strong>{{ $offer->quantity }} {{ $offer->request?->unit }}</strong></p>
                                <p><span class="text-slate-500">Total price:</span> <strong>ZMW {{ number_format((float) $offer->price, 2) }}</strong></p>
                                <p><span class="text-slate-500">Available:</span> <strong>{{ $offer->availability_date?->format('M j, Y') ?? 'Not specified' }}</strong></p>
                            </div>
                            @if($offer->message)
                                <p class="mt-4 whitespace-pre-line text-sm text-slate-700">{{ $offer->message }}</p>
                            @endif
                            @if($offer->response_message)
                                <p class="mt-3 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-900">Your note: {{ $offer->response_message }}</p>
                            @endif
                            @if($offer->status === 'submitted')
                                <form method="POST" action="{{ route('inbox.offers.respond', $offer) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                                    @csrf
                                    @method('PATCH')
                                    <label for="offer-response-{{ $offer->id }}" class="sr-only">Optional note to supplier</label>
                                    <input id="offer-response-{{ $offer->id }}" name="response_message" maxlength="1000" placeholder="Optional note to supplier" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                    <label for="fulfillment-method-{{ $offer->id }}" class="sr-only">Fulfillment method when accepting</label>
                                    <select id="fulfillment-method-{{ $offer->id }}" name="fulfillment_method" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                        <option value="collection">Buyer collects</option>
                                        <option value="delivery">Seller delivers</option>
                                    </select>
                                    <label for="fulfillment-location-{{ $offer->id }}" class="sr-only">Delivery address or location</label>
                                    <input id="fulfillment-location-{{ $offer->id }}" name="fulfillment_location" maxlength="255" placeholder="Delivery location (required for delivery)" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                    @error('fulfillment_location')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                                    @error('fulfillment_method')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                                    <button name="status" value="accepted" class="rounded-xl bg-[#14532D] px-4 py-2 text-sm font-semibold text-white">Accept & create order</button>
                                    <button name="status" value="declined" formnovalidate class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Decline</button>
                                </form>
                                <p class="mt-2 text-xs text-slate-500">Accepting an offer creates an order with the agreed quantity and price. Payment is arranged directly between buyer and seller.</p>
                            @endif
                            @if($offer->order)
                                <a href="{{ route('orders.index') }}" class="mt-4 inline-flex rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-900">Track order #{{ $offer->order->id }} · {{ str_replace('_', ' ', ucfirst($offer->order->status)) }} →</a>
                            @endif
                            @if($offer->status === 'accepted')
                                @if($offer->sellerReview)
                                    <p class="mt-4 rounded-xl bg-amber-50 p-3 text-sm font-semibold text-amber-900">Your review: {{ $offer->sellerReview->rating }} / 5</p>
                                @elseif($offer->order?->status === 'completed')
                                    <form method="POST" action="{{ route('inbox.offers.review', $offer) }}" class="mt-4 grid gap-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 sm:grid-cols-[12rem_1fr_auto] sm:items-end">
                                        @csrf
                                        <div>
                                            <label for="seller-rating-{{ $offer->id }}" class="text-sm font-semibold text-slate-700">Rate this supplier</label>
                                            <select id="seller-rating-{{ $offer->id }}" name="rating" required class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                                <option value="">Choose stars</option>
                                                <option value="5">★★★★★ Excellent</option>
                                                <option value="4">★★★★☆ Good</option>
                                                <option value="3">★★★☆☆ Okay</option>
                                                <option value="2">★★☆☆☆ Poor</option>
                                                <option value="1">★☆☆☆☆ Very poor</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="seller-review-{{ $offer->id }}" class="text-sm font-semibold text-slate-700">Buyer feedback (optional)</label>
                                            <input id="seller-review-{{ $offer->id }}" name="body" maxlength="1000" placeholder="Describe your experience with the offer" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        </div>
                                        <button class="rounded-xl bg-[#14532D] px-4 py-2.5 text-sm font-semibold text-white">Submit review</button>
                                    </form>
                                @else
                                    <p class="mt-3 text-sm text-slate-500">You can review this supplier after the order is completed.</p>
                                @endif
                            @endif
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600">No offers yet. Share a buyer request to invite suppliers to respond.</div>
                    @endforelse
                </div>
                <div class="mt-4">{{ $receivedOffers->links() }}</div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div>
                    <h2 class="mb-4 text-xl font-bold text-slate-900">Enquiries you sent</h2>
                    <div class="grid gap-3">
                        @forelse($sentEnquiries as $enquiry)
                            <article class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-slate-900">{{ $enquiry->subject }}</h3>
                                        <p class="mt-1 text-xs text-slate-500">To {{ $enquiry->recipient?->name ?? 'FarmGate user' }} · {{ $enquiry->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $enquiry->responded_at ? 'Replied' : 'Sent' }}</span>
                                </div>
                                <p class="mt-3 text-sm text-slate-700">{{ $enquiry->message }}</p>
                                @if($enquiry->response_message)
                                    <p class="mt-3 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-900"><strong>Reply:</strong> {{ $enquiry->response_message }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">Enquiries you send will show here.</p>
                        @endforelse
                    </div>
                    <div class="mt-3">{{ $sentEnquiries->links() }}</div>
                </div>
                <div>
                    <h2 class="mb-4 text-xl font-bold text-slate-900">Offers you sent</h2>
                    <div class="grid gap-3">
                        @forelse($sentOffers as $offer)
                            <article class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-slate-900">{{ $offer->request?->title ?? 'Buyer request' }}</h3>
                                        <p class="mt-1 text-xs text-slate-500">{{ $offer->quantity }} {{ $offer->request?->unit }} · ZMW {{ number_format((float) $offer->price, 2) }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($offer->status) }}</span>
                                </div>
                                @if($offer->response_message)
                                    <p class="mt-3 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-900"><strong>Buyer:</strong> {{ $offer->response_message }}</p>
                                @endif
                                @if($offer->order)
                                    <a href="{{ route('orders.index') }}" class="mt-3 inline-flex rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-900">Track order #{{ $offer->order->id }} →</a>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">Offers you send will show here.</p>
                        @endforelse
                    </div>
                    <div class="mt-3">{{ $sentOffers->links() }}</div>
                </div>
            </section>
        </div>
    </section>
@endsection
