<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Headless\DataProvider;

use App\Domain\Entity\Publication;

class PublicationDataProviderResolver extends AbstractContentDataProviderResolver
{
    public static function getDataProvider(): string
    {
        return Publication::RESOURCE_KEY;
    }
}
