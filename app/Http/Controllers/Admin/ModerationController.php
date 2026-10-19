<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\Listing;
use App\Models\ModerationEvent;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request, string $type): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        [$model, $statuses, $defaultStatus, $relations] = $this->configuration($type);
        $validated = $request->validate([
            'status' => ['sometimes', 'string', Rule::in($statuses)],
        ]);
        $status = $validated['status'] ?? $defaultStatus;

        /** @var Builder $query */
        $query = $model::query()->with([...$relations, 'moderationEvents.user'])->where('status', $status)->latest();

        return view('admin.moderation.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'type' => $type,
            'statuses' => $statuses,
            'status' => $status,
            'title' => $this->title($type),
        ]);
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        [$model] = $this->configuration($type);
        $actions = $type === 'reports'
            ? ['resolve' => 'resolved', 'dismiss' => 'dismissed', 'reopen' => 'pending']
            : ['approve' => 'approved', 'reject' => 'rejected', 'suspend' => 'suspended', 'reopen' => 'pending_review'];
        $validated = $request->validate([
            'action' => ['required', 'string', Rule::in(array_keys($actions))],
            'reason' => [
                Rule::requiredIf(fn () => in_array($request->input('action'), ['reject', 'suspend', 'dismiss'], true)),
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /** @var Model $record */
        $record = $model::findOrFail($id);
        $action = $validated['action'];
        $reason = $validated['reason'] ?? null;

        DB::transaction(function () use ($record, $actions, $action, $reason, $request): void {
            $record->status = $actions[$action];

            if ($record instanceof Listing) {
                $record->rejection_reason = $action === 'reject' ? $reason : null;
            } elseif ($record instanceof Business) {
                $record->verified_at = $action === 'approve' ? now() : null;
                $record->verification_notes = in_array($action, ['reject', 'suspend'], true) ? $reason : null;
            }

            $record->save();

            $event = new ModerationEvent([
                'user_id' => $request->user()->id,
                'action' => $action,
                'reason' => $reason,
            ]);
            $event->subject()->associate($record);
            $event->save();
        });

        return back()->with('success', ucfirst($action).' action recorded for '.$this->title($type).'.');
    }

    /**
     * @return array{class-string<Model>, list<string>, string, list<string>}
     */
    private function configuration(string $type): array
    {
        return match ($type) {
            'listings' => [Listing::class, ['pending_review', 'approved', 'rejected', 'suspended'], 'pending_review', ['user', 'category', 'images']],
            'buyer-requests' => [BuyerRequest::class, ['pending_review', 'approved', 'rejected', 'suspended'], 'pending_review', ['user', 'category']],
            'businesses' => [Business::class, ['pending_review', 'approved', 'rejected', 'suspended'], 'pending_review', ['user']],
            'reports' => [Report::class, ['pending', 'resolved', 'dismissed'], 'pending', ['user']],
            default => abort(404),
        };
    }

    private function title(string $type): string
    {
        return match ($type) {
            'listings' => 'Listings',
            'buyer-requests' => 'Buyer requests',
            'businesses' => 'Business profiles',
            'reports' => 'Reports',
            default => abort(404),
        };
    }
}
