<?php

namespace Modules\Hotel\Exceptions;

use Exception;

class HotelException extends Exception
{
    public static function notFound(int $id): self
    {
        return new self("Hotel [{$id}] not found.", 404);
    }

    public static function roomNotFound(int $id): self
    {
        return new self("Hotel room [{$id}] not found.", 404);
    }

    public static function roomUnavailable(): self
    {
        return new self('The selected room is not available for the requested dates.', 422);
    }

    public static function invalidDates(): self
    {
        return new self('Check-out date must be after check-in date.', 422);
    }
}
