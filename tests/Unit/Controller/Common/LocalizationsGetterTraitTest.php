<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Entity\Contract\PersistableEntityInterface;
use App\Tests\Application\Controller\Common\LocalizationsGetterTraitImplementation;
use PHPUnit\Framework\TestCase;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class LocalizationsGetterTraitTest extends TestCase
{
    public function testGetLocalizationsArray(): void
    {
        $route = $this->createRouteMock('/url', 'fr');

        $routeRepository = $this->createMock(RouteRepositoryInterface::class);
        $routeRepository->method('findAllByEntity')->willReturn([$route]);

        $webspaceManager = $this->createMock(WebspaceManagerInterface::class);
        $webspaceManager->method('findUrlByResourceLocator')->willReturn('https://test.fr/url');

        $class = new LocalizationsGetterTraitImplementation(
            $routeRepository,
            $webspaceManager,
        );
        $entity = $this->createPersistableEntityMock();

        $this->assertSame(
            [
                'fr' => [
                    'locale' => 'fr',
                    'url' => 'https://test.fr/url',
                ],
            ],
            $class->getLocalizationsArrayForTesting($entity),
        );
    }

    private function createRouteMock(string $path, string $locale): RouteInterface
    {
        $route = $this->createMock(RouteInterface::class);
        $route->method('getPath')->willReturn($path);
        $route->method('getLocale')->willReturn($locale);

        return $route;
    }

    private function createPersistableEntityMock(): PersistableEntityInterface
    {
        return $this->createMock(PersistableEntityInterface::class);
    }
}
