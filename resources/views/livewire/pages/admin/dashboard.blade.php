<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Admin dashboard — KROMA'])] class extends Component
{
    public function with(): array
    {
        $last7Days = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'total' => Order::whereDate('created_at', $date)
                    ->where('status', '!=', OrderStatus::Cancelled)
                    ->sum('total_cents') / 100,
            ];
        });

        $maxDay = max(1, $last7Days->max('total'));

        return [
            'revenue' => Order::where('status', '!=', OrderStatus::Cancelled)->sum('total_cents') / 100,
            'orderCount' => Order::count(),
            'lowStockCount' => \App\Models\ProductVariant::where('stock', '<', 5)->count(),
            'recentOrders' => Order::with('user')->latest()->take(6)->get(),
            'chartDays' => $last7Days,
            'chartMax' => $maxDay,
        ];
    }
}; ?>

<div>
    <section class="px-6 py-10">
        <div class="mx-auto max-w-6xl">
            <div class="flex items-center justify-between mb-10">
                <h1 class="font-display text-4xl">ADMIN</h1>
                <nav class="flex gap-4 font-mono text-xs">
                    <a href="{{ route('admin.dashboard') }}" class="underline">DASHBOARD</a>
                    <a href="{{ route('admin.products.index') }}">PRODUCTS</a>
                    <a href="{{ route('admin.orders.index') }}">ORDERS</a>
                </nav>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="card-sticker bg-riot-yellow p-6">
                    <p class="font-mono text-xs">TOTAL REVENUE</p>
                    <p class="font-display text-3xl mt-2">{{ number_format($revenue, 2) }} RON</p>
                </div>
                <div class="card-sticker bg-electric text-bone p-6">
                    <p class="font-mono text-xs">ORDERS</p>
                    <p class="font-display text-3xl mt-2">{{ $orderCount }}</p>
                </div>
                <div class="card-sticker bg-flash-coral text-bone p-6">
                    <p class="font-mono text-xs">LOW STOCK VARIANTS (&lt;5)</p>
                    <p class="font-display text-3xl mt-2">{{ $lowStockCount }}</p>
                </div>
            </div>

            {{-- Simple hand-rolled SVG bar chart — no chart.js dependency needed for 7 bars --}}
            <div class="card-sticker bg-white p-6 mb-10">
                <p class="font-mono text-xs mb-4">REVENUE — LAST 7 DAYS</p>
                <div class="flex items-end gap-4 h-40">
                    @foreach ($chartDays as $day)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-ink" style="height: {{ max(4, ($day['total'] / $chartMax) * 100) }}%"></div>
                            <span class="font-mono text-[10px] text-static-grey">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-sticker bg-white p-6">
                <p class="font-mono text-xs mb-4">RECENT ORDERS</p>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left font-mono text-xs text-static-grey border-b-2 border-ink">
                            <th class="py-2">REF</th>
                            <th>CUSTOMER</th>
                            <th>STATUS</th>
                            <th class="text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr class="border-b border-static-grey/30">
                                <td class="py-2 font-mono text-xs">
                                    <a href="{{ route('admin.orders.index') }}" class="underline">{{ $order->reference }}</a>
                                </td>
                                <td>{{ $order->user->name }}</td>
                                <td><x-tag class="!py-0 !px-2 !text-xs !shadow-none">{{ $order->status->label() }}</x-tag></td>
                                <td class="text-right font-mono">{{ number_format($order->total_cents / 100, 2) }} RON</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
