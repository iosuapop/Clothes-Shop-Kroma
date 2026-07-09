<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background: #F6F3EC; margin: 0; padding: 32px;">
    <table role="presentation" width="100%" style="max-width: 480px; margin: 0 auto; background: #ffffff; border: 3px solid #131313;">
        <tr>
            <td style="background: #F3E600; padding: 24px; border-bottom: 3px solid #131313;">
                <h1 style="margin: 0; font-size: 24px; letter-spacing: -0.5px;">KROMA</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin-top: 0;">Order confirmed</h2>
                <p>Thanks for your order, {{ $order->user->name }}. We're getting it ready.</p>

                <p style="font-family: monospace; background: #F6F3EC; padding: 8px 12px; display: inline-block; border: 2px solid #131313;">
                    {{ $order->reference }}
                </p>

                <table role="presentation" width="100%" style="margin-top: 16px; border-collapse: collapse;">
                    @foreach ($order->items as $item)
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #8A8A82;">
                                {{ $item->product_name }} ({{ $item->size }}) &times; {{ $item->quantity }}
                            </td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #8A8A82; text-align: right;">
                                {{ number_format($item->unit_price_cents * $item->quantity / 100, 2) }} RON
                            </td>
                        </tr>
                    @endforeach
                </table>

                <p style="text-align: right; font-weight: bold; margin-top: 16px;">
                    Total: {{ number_format($order->total_cents / 100, 2) }} RON
                </p>

                <p>Shipping to: {{ $order->shipping_address }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
