<?php

namespace Modules\Space\Actions;

use Modules\Space\DTOs\PrebookSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Providers\SpaceProviderInterface;

class PrebookSpaceAction
{
    public function __construct(private SpaceProviderInterface $provider) {}

    public function handle(PrebookSpaceDto $dto): SpaceOfferDto
    {
        return $this->provider->prebook($dto);
    }
}
