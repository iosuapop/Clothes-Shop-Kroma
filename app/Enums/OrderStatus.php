<?php

namespace App\Enums;

/**
 * Native PHP enum instead of a free-text string column: invalid statuses
 * become impossible to store, and IDE autocomplete replaces "guessing"
 * the right string everywhere the status is checked.
 */
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending payment',
            self::Paid => 'Paid',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /** Tailwind color token used consistently for status badges in the admin panel. */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'static-grey',
            self::Paid, self::Processing => 'electric',
            self::Shipped, self::Delivered => 'riot-yellow',
            self::Cancelled => 'flash-coral',
        };
    }
}
