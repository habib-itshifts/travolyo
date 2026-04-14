<?php

namespace Modules\Space\Exceptions;

use Exception;

class SpaceException extends Exception
{
    public static function notFound(int $id): self
    {
        return new self("Space [{$id}] not found.", 404);
    }

    public static function unavailable(): self
    {
        return new self('The selected space is not available for the requested dates.', 422);
    }

    public static function invalidDates(): self
    {
        return new self('Check-out date must be after check-in date.', 422);
    }

    public static function doubleBooking(): self
    {
        return new self('This space is already booked for one or more of the requested dates.', 409);
    }

    public static function minStayNotMet(int $minNights): self
    {
        return new self("Minimum stay of {$minNights} nights is required.", 422);
    }

    public static function maxStayExceeded(int $maxNights): self
    {
        return new self("Maximum stay of {$maxNights} nights exceeded.", 422);
    }

    public static function checkoutExpired(): self
    {
        return new self('Checkout session has expired. Please start the booking process again.', 410);
    }
}
