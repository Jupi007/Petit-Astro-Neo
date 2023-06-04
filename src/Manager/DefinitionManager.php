<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\Definition\CreatedDefinitionEvent;
use App\DomainEvent\Definition\ModifiedDefinitionEvent;
use App\DomainEvent\Definition\RemovedDefinitionEvent;
use App\DomainEvent\Definition\TranslationCopiedDefinitionEvent;
use App\DTO\Definition\CreateDefinitionDTO;
use App\DTO\Definition\UpdateDefinitionDTO;
use App\Entity\Definition;
use App\Repository\DefinitionRepositoryInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RouteManagerInterface $routeManager,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function create(CreateDefinitionDTO $dto): Definition
    {
        $definition = (new Definition())
            ->setLocale($dto->locale)
            ->setTitle($dto->title)
            ->setDescription($dto->description);

        $this->repository->save($definition);

        $this->createOrUpdateRoute($definition, $dto->routePath);

        $this->eventDispatcher->dispatch(new CreatedDefinitionEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function update(UpdateDefinitionDTO $dto): Definition
    {
        $definition = $this->repository->getOne($dto->id)
            ->setLocale($dto->locale)
            ->setTitle($dto->title)
            ->setDescription($dto->description);
        $this->createOrUpdateRoute($definition, $dto->routePath);

        $this->eventDispatcher->dispatch(new ModifiedDefinitionEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function copyLocale(int $id, string $srcLocale, string $destLocale): Definition
    {
        $definition = $this->repository->getOneLocalized($id, $srcLocale);

        $srcTitle = $definition->getTitle() ?? '';
        $srcDescription = $definition->getDescription() ?? '';
        $srcRoutePath = $definition->getRoute()?->getPath() ?? '';

        $definition
            ->setLocale($destLocale)
            ->setTitle($srcTitle)
            ->setDescription($srcDescription);
        $this->createOrUpdateRoute($definition, $srcRoutePath);

        $this->eventDispatcher->dispatch(
            new TranslationCopiedDefinitionEvent(
                resource: $definition,
                srcLocale: $srcLocale,
                destLocale: $destLocale,
            ),
        );
        $this->repository->save($definition);

        return $definition;
    }

    public function remove(int $id): void
    {
        $definition = $this->repository->getOne($id);

        $this->eventDispatcher->dispatch(new RemovedDefinitionEvent($definition));
        $this->removeRoutes($definition);
        $this->repository->remove($definition);
    }

    private function createOrUpdateRoute(Definition $definition, string $routePath): void
    {
        if (!$definition->getRoute() instanceof RouteInterface) {
            $this->routeManager->create($definition, $routePath);
        }

        $this->routeManager->update($definition, $routePath);
    }

    private function removeRoutes(Definition $definition): void
    {
        foreach ($definition->getLocales() as $locale) {
            $definition->setLocale($locale);

            if (($route = $definition->getRoute()) instanceof RouteInterface) {
                $this->routeRepository->remove($route);

                foreach ($route->getHistories() as $historyRoute) {
                    $this->routeRepository->remove($historyRoute);
                }
            }
        }
    }
}
