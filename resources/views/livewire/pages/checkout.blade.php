<?php

use App\Actions\PlaceOrder;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Checkout — KROMA'])] class extends Component
{
    public string $address = '';
    public string $phone = '';
    public string $clientSecret = '';
    public float $total = 0;

    public function mount(CheckoutService $checkout): void
    {
        $cart = new CartService(Auth::user(), session()->getId());

        // items() runs a fresh query every time it's called; grab it once
        // and reuse it instead of calling items()/totalCents() separately
        // (each of those re-queries and re-eager-loads the whole cart).
        $items = $cart->items();

        if ($items->isEmpty()) {
            $this->redirect(route('cart.show'), navigate: true);

            return;
        }

        $totalCents = $items->sum(fn ($item) => $item->lineTotalCents());
        $this->total = $totalCents / 100;
        $this->address = Auth::user()->address ?? '';
        $this->phone = Auth::user()->phone ?? '';

        // Created once per page load; the card element on the frontend
        // confirms against this specific PaymentIntent.
        $intent = $checkout->createPaymentIntent($totalCents);
        $this->clientSecret = $intent->client_secret;
    }

    /** Called from JS once Stripe confirms the card payment succeeded. */
    public function finalizeOrder(string $paymentIntentId, CheckoutService $checkout): void
    {
        $this->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
        ]);

        $payment = $checkout->confirmSucceeded($paymentIntentId);

        if (! $payment) {
            $this->addError('address', 'Payment could not be verified. Please try again.');

            return;
        }

        // PlaceOrder needs a CartService built with the current user/session
        // — the container can't build that on its own, so it's constructed
        // manually here instead of type-hinting PlaceOrder as a parameter.
        $cart = new CartService(Auth::user(), session()->getId());
        $order = (new PlaceOrder($cart))($this->user(), $this->address, $this->phone, $payment);

        $this->redirect(route('account.orders', ['confirmed' => $order->reference]), navigate: true);
    }

    private function user()
    {
        return Auth::user();
    }
}; ?>

<div>
    <section class="px-6 py-14">
        <div class="mx-auto max-w-xl">
            <h1 class="font-display text-4xl mb-10">CHECKOUT</h1>

            <form id="checkout-form" class="space-y-4" x-data="checkoutForm(@js($clientSecret))" @submit.prevent="pay">
                <div>
                    <label class="font-mono text-xs">SHIPPING ADDRESS</label>
                    <input type="text" wire:model="address" class="w-full border-2 border-ink p-2 mt-1">
                    @error('address') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-mono text-xs">PHONE</label>
                    <input type="text" wire:model="phone" class="w-full border-2 border-ink p-2 mt-1">
                    @error('phone') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-mono text-xs">CARD</label>
                    <div id="card-element" class="border-2 border-ink p-3 mt-1 bg-white"></div>
                    <p x-show="cardError" x-text="cardError" class="text-flash-coral text-xs mt-1"></p>
                </div>

                <div class="flex justify-between items-center font-display text-2xl pt-4">
                    <span>TOTAL</span>
                    <x-tag>{{ number_format($total, 2) }} RON</x-tag>
                </div>

                <button type="submit" :disabled="processing"
                        class="card-sticker bg-riot-yellow font-display w-full py-4 text-lg disabled:opacity-50">
                    <span x-show="!processing">PAY NOW</span>
                    <span x-show="processing">PROCESSING…</span>
                </button>
            </form>
        </div>
    </section>

    @script
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Registered as an Alpine component so the Stripe Elements card
        // field (which Livewire can't see inside, since it's an iframe)
        // stays outside Livewire's DOM diffing while everything else on
        // the page remains reactive.
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkoutForm', (clientSecret) => ({
                processing: false,
                cardError: '',
                stripe: null,
                elements: null,
                card: null,

                init() {
                    this.stripe = Stripe('{{ config('services.stripe.key') }}');
                    this.elements = this.stripe.elements({ clientSecret });
                    this.card = this.elements.create('payment');
                    this.card.mount('#card-element');
                },

                async pay() {
                    this.processing = true;
                    this.cardError = '';

                    const { error, paymentIntent } = await this.stripe.confirmPayment({
                        elements: this.elements,
                        redirect: 'if_required',
                    });

                    if (error) {
                        this.cardError = error.message;
                        this.processing = false;
                        return;
                    }

                    await $wire.finalizeOrder(paymentIntent.id);
                },
            }));
        });
    </script>
    @endscript
</div>
