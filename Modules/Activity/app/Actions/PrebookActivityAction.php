<?php

namespace Modules\Activity\Actions;

use Modules\Activity\DTOs\ActivityOfferDto;
use Modules\Activity\DTOs\PrebookActivityDto;
use Modules\Activity\Enums\ActivityProviderEnum;
use Modules\Activity\Providers\ActivityProviderInterface;
use Modules\Activity\Providers\Local\LocalActivityProvider;

class PrebookActivityAction
{
    public function handle(PrebookActivityDto $dto): ActivityOfferDto
    {
        return $this->resolveProvider($dto->provider)->prebook($dto);
    }

    private function resolveProvider(ActivityProviderEnum $provider): ActivityProviderInterface
    {
        return match ($provider) {
            ActivityProviderEnum::Local => new LocalActivityProvider(),
        };
    }
}
