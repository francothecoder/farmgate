<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $orders = Order::with(['offer.request', 'buyer', 'seller', 'updates.user'])
            ->where(fn ($query) => $query
                ->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id))
            ->latest()
            ->paginate(12);

        return view('orders.index', compact('orders'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->seller_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in($this->sellerNextStatuses($order))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $request, $validated): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $nextStatuses = $this->sellerNextStatuses($lockedOrder);
            abort_unless(in_array($validated['status'], $nextStatuses, true), 409);

            $lockedOrder->update(['status' => $validated['status']]);
            $lockedOrder->updates()->create([
                'user_id' => $request->user()->id,
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
            ]);
        });

        return back()->with('success', 'Order status updated to '.str_replace('_', ' ', $validated['status']).'.');
    }

    public function complete(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->buyer_id === (int) $request->user()->id, 403);

        DB::transaction(function () use ($order, $request): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $readyStatus = $lockedOrder->fulfillment_method === 'delivery' ? 'delivered' : 'ready_for_pickup';
            abort_unless($lockedOrder->status === $readyStatus, 409);

            $lockedOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $lockedOrder->updates()->create([
                'user_id' => $request->user()->id,
                'status' => 'completed',
                'note' => 'Buyer confirmed receipt of the order.',
            ]);
        });

        return back()->with('success', 'Order marked complete. You can now leave a review for the seller.');
    }

    /**
     * @return list<string>
     */
    private function sellerNextStatuses(Order $order): array
    {
        return match ([$order->status, $order->fulfillment_method]) {
            ['confirmed', 'collection'], ['confirmed', 'delivery'] => ['preparing'],
            ['preparing', 'collection'] => ['ready_for_pickup'],
            ['preparing', 'delivery'] => ['dispatched'],
            ['dispatched', 'delivery'] => ['delivered'],
            default => [],
        };
    }
}
