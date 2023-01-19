<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Definition;
use App\Event\Definition\CreatedDefinitionActivityEvent;
use App\Event\Definition\ModifiedDefinitionActivityEvent;
use App\Event\Definition\RemovedDefinitionActivityEvent;
use App\Repository\DefinitionRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly TrashManagerInterface $trashManager,
        private readonly RouteManagerInterface $routeManager,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function createFromRequest(Request $request): Definition
    {
        $definition = new Definition();

        $this->mapRequestToDefinition($definition, $request);
        $this->domainEventCollector->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        $this->generateDefinitionRoute($definition, $request);
        $this->repository->save($definition);

        return $definition;
    }

    public function updateFromRequest(Definition $definition, Request $request): Definition
    {
        $this->mapRequestToDefinition($definition, $request);
        $this->generateDefinitionRoute($definition, $request);
        $this->domainEventCollector->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->trashManager->store(Definition::RESOURCE_KEY, $definition);
        $this->domainEventCollector->collect(new RemovedDefinitionActivityEvent($definition));
        $this->removeRoutes($definition);
        $this->repository->remove($definition);
    }

    private function mapRequestToDefinition(Definition $definition, Request $request): void
    {
        $data = $request->toArray();
        $locale = $request->query->get('locale');

        $definition
            ->setLocale($locale ?? '')
            ->setTitle($data['title'] ?? '')
            ->setContent($data['content'] ?? '');
    }

    private function generateDefinitionRoute(Definition $definition, Request $request): void
    {
        $route = (string) $request->toArray()['routePath'];

        if (null === $definition->getRoute()) {
            $this->routeManager->create($definition, $route);
        } elseif ($definition->getRoute()->getPath() !== $route) {
            $this->routeManager->update($definition, $route);
        }
    }

    private function removeRoutes(Definition $definition): void
    {
        foreach ($definition->getLocales() as $locale) {
            $definition->setLocale($locale);

            if (null !== $route = $definition->getRoute()) {
                $this->routeRepository->remove($route);

                foreach ($route->getHistories() as $historyRoute) {
                    $this->routeRepository->remove($historyRoute);
                }
            }
        }
    }
}
