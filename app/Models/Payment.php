<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = ['amount_cents', 'currency', 'method', 'status', 'stripe_payment_intent_id'];

    protected $casts = ['status' => PaymentStatus::class];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
