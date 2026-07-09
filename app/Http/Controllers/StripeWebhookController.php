<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

/**
 * The checkout page already confirms payment synchronously (see
 * CheckoutService::confirmSucceeded), which is enough for a thesis demo.
 * This webhook is the production-grade hardening on top: Stripe calls
 * it directly, so a payment that succeeds after the customer closes
 * their browser tab still gets recorded correctly.
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;

            Payment::updateOrCreate(
                ['stripe_payment_intent_id' => $intent->id],
                [
                    'amount_cents' => $intent->amount,
                    'currency' => $intent->currency,
                    'method' => 'card',
                    'status' => PaymentStatus::Succeeded,
                ]
            );
        }

        if ($event->type === 'payment_intent.payment_failed') {
            Payment::where('stripe_payment_intent_id', $event->data->object->id)
                ->update(['status' => PaymentStatus::Failed]);
        }

        return response('OK', 200);
    }
}
