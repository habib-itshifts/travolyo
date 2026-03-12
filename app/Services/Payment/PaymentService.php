<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

class PaymentService
{
    /**
     * Resolve a gateway by name.
     *
     * Usage:
     *   $url = PaymentService::gateway('stripe')->initiate($booking)['url'];
     */
    public static function gateway(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'stripe'  => new StripeGateway(),
            'ngenius' => new NGeniusGateway(),
            default   => throw new \InvalidArgumentException("Unknown payment gateway: [{$name}]"),
        };
    }

    /**
     * List available gateway IDs.
     *
     * @return string[]
     */
    public static function available(): array
    {
        return ['stripe', 'ngenius'];
    }
}
