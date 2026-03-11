<?php

namespace Modules\Flight\Exceptions;

use RuntimeException;

class FlightException extends RuntimeException
{
    public static function providerError(string $provider, string $message, int $code = 0): self
    {
        return new self("[{$provider}] {$message}", $code);
    }

    public static function offerNotFound(string $offerId): self
    {
        return new self("Flight offer not found: {$offerId}", 404);
    }

    public static function orderNotFound(string $orderId): self
    {
        return new self("Flight order not found: {$orderId}", 404);
    }
}
