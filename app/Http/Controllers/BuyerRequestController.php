<?php

namespace App\Http\Controllers;

use App\Models\BuyerOffer;
use App\Models\BuyerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $request->validate([
            'quantity' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'availability_date' => ['nullable', 'date'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        BuyerOffer::create([
            'buyer_request_id' => $buyerRequest->id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'price' => $request->price,
            'availability_date' => $request->availability_date,
            'message' => $request->message,
            'status' => 'submitted',
        ]);

        return back()->with('success', 'Your offer has been submitted successfully.');
    }
}
