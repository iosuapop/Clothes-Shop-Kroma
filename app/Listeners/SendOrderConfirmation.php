<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Implements ShouldQueue: sending the confirmation email happens on the
 * queue worker, not in the request/response cycle. The customer sees
 * the "order confirmed" page instantly instead of waiting on SMTP.
 */
class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        Mail::to($event->order->user)->send(new OrderConfirmationMail($event->order));
    }
}
