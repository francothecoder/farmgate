<?php

namespace App\Http\Controllers;

use App\Models\BuyerOffer;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $receivedEnquiries = Enquiry::with('sender')
            ->where('recipient_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'received_page');
        $sentEnquiries = Enquiry::with('recipient')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'sent_page');
        $receivedOffers = BuyerOffer::with(['user', 'request', 'sellerReview', 'order'])
            ->whereHas('request', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(10, ['*'], 'offers_page');
        $sentOffers = BuyerOffer::with(['request', 'order'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'sent_offers_page');

        return view('inbox.index', compact('receivedEnquiries', 'sentEnquiries', 'receivedOffers', 'sentOffers'));
    }

    public function reply(Request $request, Enquiry $enquiry): RedirectResponse
    {
        abort_unless((int) $enquiry->recipient_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'response_message' => ['required', 'string', 'max:1000'],
        ]);

        $enquiry->update([
            'response_message' => $validated['response_message'],
            'responded_at' => now(),
            'status' => 'replied',
        ]);

        return back()->with('success', 'Your reply has been sent to the enquirer.');
    }

    public function respondToOffer(Request $request, BuyerOffer $buyerOffer): RedirectResponse
    {
        abort_unless(
            $buyerOffer->request()->where('user_id', $request->user()->id)->exists(),
            403,
        );
        abort_unless($buyerOffer->status === 'submitted', 409);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,declined'],
            'response_message' => ['nullable', 'string', 'max:1000'],
            'fulfillment_method' => ['required_if:status,accepted', 'nullable', 'in:collection,delivery'],
            'fulfillment_location' => [
                Rule::requiredIf(fn () => $request->input('status') === 'accepted' && $request->input('fulfillment_method') === 'delivery'),
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($buyerOffer, $validated, $request): void {
            $offer = BuyerOffer::query()->lockForUpdate()->findOrFail($buyerOffer->id);
            abort_unless($offer->status === 'submitted', 409);

            $offer->update([
                'status' => $validated['status'],
                'response_message' => $validated['response_message'] ?? null,
                'responded_at' => now(),
            ]);

            if ($validated['status'] === 'accepted') {
                $offer->loadMissing('request');
                $order = $offer->order()->create([
                    'buyer_id' => $request->user()->id,
                    'seller_id' => $offer->user_id,
                    'quantity' => $offer->quantity,
                    'unit' => $offer->request?->unit,
                    'total_price' => $offer->price,
                    'currency' => 'ZMW',
                    'fulfillment_method' => $validated['fulfillment_method'],
                    'fulfillment_location' => $validated['fulfillment_location'] ?? null,
                    'status' => 'confirmed',
                ]);
                $order->updates()->create([
                    'user_id' => $request->user()->id,
                    'status' => 'confirmed',
                    'note' => 'Offer accepted and order confirmed.',
                ]);
            }
        });

        $message = $validated['status'] === 'accepted'
            ? 'Offer accepted. Your order has been created and is ready to track.'
            : 'Offer declined. The supplier can see your decision in their inbox.';

        return back()->with('success', $message);
    }
}
