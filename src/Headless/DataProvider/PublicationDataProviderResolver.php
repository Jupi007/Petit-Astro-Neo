<?php

declare(strict_types=1);

namespace App\Headless\DataProvider;

use App\Entity\Publication;

class PublicationDataProviderResolver extends AbstractContentDataProviderResolver
{
    public static function getDataProvider(): string
    {
        return Publication::RESOURCE_KEY;
    }
}
