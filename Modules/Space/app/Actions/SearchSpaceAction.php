<?php

namespace Modules\Space\Actions;

use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Providers\SpaceProviderInterface;

class SearchSpaceAction
{
    public function __construct(private SpaceProviderInterface $provider) {}

    /** @return SpaceOfferDto[] */
    public function handle(SearchSpaceDto $dto): array
    {
        return $this->provider->search($dto);
    }
}
