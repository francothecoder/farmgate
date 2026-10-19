<?php

namespace App\Http\Controllers;

use App\Models\BuyerOffer;
use App\Models\BuyerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BuyerRequestController extends Controller
{
    public function index(): View
    {
        $requests = Schema::hasTable('buyer_requests')
            ? BuyerRequest::with(['category', 'user'])->where('status', 'approved')->latest()->paginate(10)
            : collect();

        return view('buyer-requests.index', compact('requests'));
    }

    public function show(BuyerRequest $buyerRequest): View
    {
        abort_unless($buyerRequest->status === 'approved', 404);

        $buyerRequest->load(['category', 'user', 'offers.user']);

        return view('buyer-requests.show', compact('buyerRequest'));
    }

    public function storeOffer(Request $request, BuyerRequest $buyerRequest)
    {
        abort_unless($buyerRequest->status === 'approved', 404);
        abort_if((int) $buyerRequest->user_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'availability_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->buyerOffers()->create([
            'buyer_request_id' => $buyerRequest->id,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'availability_date' => $validated['availability_date'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'submitted',
        ]);

        return back()->with('success', 'Your offer has been submitted successfully.');
    }
}
