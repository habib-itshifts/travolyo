<?php

namespace Modules\Activity\Exceptions;

use Exception;

class ActivityException extends Exception
{
    public static function notFound(string $id): self
    {
        return new self("Activity [{$id}] not found.", 404);
    }

    public static function unavailable(): self
    {
        return new self('The selected activity is not available.', 422);
    }

    public static function invalidDate(): self
    {
        return new self('Activity date must be today or in the future.', 422);
    }

    public static function maxParticipantsExceeded(int $max): self
    {
        return new self("Participants cannot exceed {$max}.", 422);
    }

    public static function sessionExpired(): self
    {
        return new self('Activity session expired. Please search and select your activity again.', 410);
    }
}
