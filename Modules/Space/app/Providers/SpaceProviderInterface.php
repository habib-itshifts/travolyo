<?php

namespace Modules\Space\Providers;

use Modules\Space\DTOs\PrebookSpaceDto;
use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;

interface SpaceProviderInterface
{
    /**
     * Search available spaces and return a flat list of offers.
     *
     * @return SpaceOfferDto[]
     */
    public function search(SearchSpaceDto $dto): array;

    /**
     * Validate availability + constraints and calculate final pricing.
     * For external providers this may call their hold/reserve API.
     */
    public function prebook(PrebookSpaceDto $dto): SpaceOfferDto;
}
