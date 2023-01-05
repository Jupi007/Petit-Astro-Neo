<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Definition;
use App\Event\Definition\CreatedDefinitionActivityEvent;
use App\Event\Definition\ModifiedDefinitionActivityEvent;
use App\Event\Definition\RemovedDefinitionActivityEvent;
use App\Repository\DefinitionRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly TrashManagerInterface $trashManager,
    ) {
    }

    public function createFromRequest(Request $request): Definition
    {
        $definition = $this->mapRequestToDefinition(new Definition(), $request);

        $this->domainEventCollector->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function updateFromRequest(Definition $definition, Request $request): Definition
    {
        $definition = $this->mapRequestToDefinition($definition, $request);

        $this->domainEventCollector->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->domainEventCollector->collect(new RemovedDefinitionActivityEvent($definition));
        $this->trashManager->store(Definition::RESOURCE_KEY, $definition);
        $this->repository->remove($definition);
    }

    private function mapRequestToDefinition(Definition $definition, Request $request): Definition
    {
        $data = $request->toArray();
        $locale = $request->query->get('locale');

        $definition->setLocale($locale ?? '');
        $definition->setTitle($data['title'] ?? '');
        $definition->setContent($data['content'] ?? '');

        return $definition;
    }
}
