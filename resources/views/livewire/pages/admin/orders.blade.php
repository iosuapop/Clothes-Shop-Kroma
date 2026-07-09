<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app', ['title' => 'Orders — Admin'])] class extends Component
{
    use WithPagination;

    public function updateStatus(Order $order, string $status): void
    {
        $order->update(['status' => OrderStatus::from($status)]);
    }

    public function with(): array
    {
        return [
            'orders' => Order::with(['user', 'items'])->latest()->paginate(15),
            'statuses' => OrderStatus::cases(),
        ];
    }
}; ?>

<div>
    <section class="px-6 py-10">
        <div class="mx-auto max-w-6xl">
            <h1 class="font-display text-4xl mb-10">ORDERS</h1>

            <div class="card-sticker bg-white overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left font-mono text-xs text-static-grey border-b-2 border-ink">
                            <th class="p-3">REF</th>
                            <th>CUSTOMER</th>
                            <th>ITEMS</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border-b border-static-grey/30">
                                <td class="p-3 font-mono text-xs">{{ $order->reference }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td class="font-mono text-xs">{{ $order->items->count() }}</td>
                                <td class="font-mono">{{ number_format($order->total_cents / 100, 2) }} RON</td>
                                <td>
                                    <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                            class="border-2 border-ink font-mono text-xs p-1">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}" @selected($order->status === $status)>
                                                {{ strtoupper($status->label()) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        </div>
    </section>
</div>
