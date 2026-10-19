<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', Rule::in(['listing', 'business'])],
            'target_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', Rule::in(['misleading', 'unavailable', 'scam', 'inappropriate', 'other'])],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $model = $validated['target_type'] === 'listing' ? Listing::class : Business::class;
        $target = $model::query()
            ->where('status', 'approved')
            ->when($model === Listing::class, fn ($query) => $query->where('is_active', true))
            ->findOrFail($validated['target_id']);

        abort_if((int) $target->user_id === (int) $request->user()->id, 403);

        $alreadyReported = Report::query()
            ->whereBelongsTo($request->user())
            ->where('reportable_type', $model)
            ->where('reportable_id', $target->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReported) {
            throw ValidationException::withMessages([
                'target_id' => 'You have already reported this item. Our team is reviewing it.',
            ]);
        }

        Report::create([
            'user_id' => $request->user()->id,
            'reportable_type' => $model,
            'reportable_id' => $target->id,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
        ]);

        return back()->with('success', 'Thanks for helping keep FarmGate safe. Our team will review your report.');
    }
}
