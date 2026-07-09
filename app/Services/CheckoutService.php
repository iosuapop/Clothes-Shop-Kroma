<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

/**
 * Thin wrapper around the Stripe SDK. Keeping it here (instead of calling
 * Stripe directly from the Livewire checkout component) means the
 * component doesn't need to know Stripe exists — swapping payment
 * providers later touches this one class.
 */
class CheckoutService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPaymentIntent(int $amountCents): PaymentIntent
    {
        return $this->stripe->paymentIntents->create([
            'amount' => max($amountCents, 100), // Stripe requires a minimum charge
            'currency' => 'ron',
            'automatic_payment_methods' => ['enabled' => true],
        ]);
    }

    /**
     * Re-fetches the PaymentIntent from Stripe rather than trusting the
     * client-side JS callback — the browser could be lying (or a bug
     * could fire the success event without a real charge).
     */
    public function confirmSucceeded(string $paymentIntentId): ?Payment
    {
        $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

        if ($intent->status !== 'succeeded') {
            return null;
        }

        return Payment::create([
            'amount_cents' => $intent->amount,
            'currency' => $intent->currency,
            'method' => 'card',
            'status' => PaymentStatus::Succeeded,
            'stripe_payment_intent_id' => $intent->id,
        ]);
    }
}
