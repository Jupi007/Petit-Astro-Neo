<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Entity\Contract\PersistableEntityInterface;
use App\Tests\Unit\Controller\Common\LocalizationsGetterTraitImplementation;
use PHPUnit\Framework\TestCase;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class LocalizationsGetterTraitTest extends TestCase
{
    public function testGetLocale(): void
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
        $entity = $this->createPersistableEntity();

        $this->assertSame([
            'fr' => [
                'locale' => 'fr',
                'url' => 'https://test.fr/url',
            ],
        ], $class->getLocalizationsArrayForTesting($entity));
    }

    private function createRouteMock(string $path, string $locale): RouteInterface
    {
        $route = $this->createMock(RouteInterface::class);
        $route->method('getPath')->willReturn($path);
        $route->method('getLocale')->willReturn($locale);

        return $route;
    }

    private function createPersistableEntity(): PersistableEntityInterface
    {
        return new class() implements PersistableEntityInterface {
            public function getId(): ?int
            {
                return 1;
            }
        };
    }
}
