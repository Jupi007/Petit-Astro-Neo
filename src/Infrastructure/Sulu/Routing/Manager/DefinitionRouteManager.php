<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Routing\Manager;

use App\DomainEvent\Definition\CreatedDefinitionEvent;
use App\DomainEvent\Definition\ModifiedDefinitionEvent;
use App\DomainEvent\Definition\RemovedDefinitionEvent;
use App\DomainEvent\Definition\TranslationCopiedDefinitionEvent;
use App\Entity\Definition;
use App\Exception\NullAssertionException;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DefinitionRouteManager implements EventSubscriberInterface
{
    public function __construct(
        private readonly RouteManagerInterface $routeManager,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreatedDefinitionEvent::class => 'onCreatedModifiedDefinition',
            ModifiedDefinitionEvent::class => 'onCreatedModifiedDefinition',
            TranslationCopiedDefinitionEvent::class => 'onTranslationCopiedDefinition',
            RemovedDefinitionEvent::class => 'onRemovedDefinition',
        ];
    }

    public function onCreatedModifiedDefinition(CreatedDefinitionEvent|ModifiedDefinitionEvent $event): void
    {
        $definition = $event->getResource();

        $route = $this->routeManager->createOrUpdateByAttributes(
            Definition::class,
            (string) $definition->getId(),
            $definition->getLocale(),
            $definition->getRoutePath() ?? throw new NullAssertionException(),
        );

        $definition->setRoutePath($route->getPath());
    }

    public function onTranslationCopiedDefinition(TranslationCopiedDefinitionEvent $event): void
    {
        $definition = $event->getResource();

        $route = $this->routeManager->createOrUpdateByAttributes(
            Definition::class,
            (string) $definition->getId(),
            $event->getDestLocale(),
            $definition->getRoutePath() ?? throw new NullAssertionException(),
        );

        $definition->setRoutePath($route->getPath());
    }

    public function onRemovedDefinition(RemovedDefinitionEvent $event): void
    {
        $definition = $event->getResource();

        $routes = $this->routeRepository->findAllByEntity(
            Definition::class,
            (string) $definition->getId(),
        );

        foreach ($routes as $route) {
            $this->routeRepository->remove($route);
        }
    }
}
