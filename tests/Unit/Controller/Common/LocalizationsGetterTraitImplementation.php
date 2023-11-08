<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Common;

use App\Controller\Common\LocalizationsGetterTrait;
use App\Entity\Contract\PersistableEntityInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class LocalizationsGetterTraitImplementation
{
    use LocalizationsGetterTrait;

    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
    }

    /** @return array<string, array{locale: string, url:string|null}> */
    public function getLocalizationsArrayForTesting(PersistableEntityInterface $entity): array
    {
        return $this->getLocalizationsArray($entity);
    }
}
