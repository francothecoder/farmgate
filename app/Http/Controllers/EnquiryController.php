<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\Enquiry;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'related_type' => ['required', Rule::in(['listing', 'business', 'buyer_request'])],
            'related_id' => ['required', 'integer', 'min:1'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $resource = match ($validated['related_type']) {
            'listing' => Listing::where('status', 'approved')->where('is_active', true)->findOrFail($validated['related_id']),
            'business' => Business::where('status', 'approved')->findOrFail($validated['related_id']),
            'buyer_request' => BuyerRequest::where('status', 'approved')->findOrFail($validated['related_id']),
        };

        abort_if((int) $resource->user_id === (int) $request->user()->id, 403);

        $request->user()->sentEnquiries()->create([
            'recipient_id' => $resource->user_id,
            'related_type' => $validated['related_type'],
            'related_id' => $resource->id,
            'subject' => 'Enquiry about '.($resource->title ?? $resource->name),
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return back()->with('success', 'Your message has been sent. You can follow replies from your inbox.');
    }
}
