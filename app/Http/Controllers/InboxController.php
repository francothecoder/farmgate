<?php

namespace App\Http\Controllers;

use App\Models\BuyerOffer;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $receivedOffers = BuyerOffer::with(['user', 'request', 'sellerReview'])
            ->whereHas('request', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(10, ['*'], 'offers_page');
        $sentOffers = BuyerOffer::with('request')
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
        ]);

        DB::transaction(function () use ($buyerOffer, $validated): void {
            $buyerOffer->update([
                'status' => $validated['status'],
                'response_message' => $validated['response_message'] ?? null,
                'responded_at' => now(),
            ]);
        });

        return back()->with('success', 'Offer '.$validated['status'].'. The supplier can see your decision in their inbox.');
    }
}
