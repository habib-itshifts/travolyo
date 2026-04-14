<?php

namespace Modules\Space\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class PrebookSpaceDto
{
    public function __construct(
        public readonly int     $spaceId,
        public readonly string  $checkIn,
        public readonly string  $checkOut,
        public readonly int     $adults,
        public readonly int     $children = 0,
        public readonly int     $infants  = 0,
        public readonly int     $guests   = 1,
        public readonly string  $currency = 'USD',
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        $adults   = max(1, (int) $request->input('adults', 1));
        $children = max(0, (int) $request->input('children', 0));
        $infants  = max(0, (int) $request->input('infants', 0));

        return new self(
            spaceId:  (int) $request->input('space_id'),
            checkIn:  $request->input('check_in'),
            checkOut: $request->input('check_out'),
            adults:   $adults,
            children: $children,
            infants:  $infants,
            guests:   max(1, $adults + $children + $infants),
            currency: $request->input('currency', currency()->getUserCurrency()),
        );
    }
}
