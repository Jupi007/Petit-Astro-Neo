<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Definition;
use App\Entity\Factory\DefinitionFactory;
use App\Event\Definition\CreatedDefinitionActivityEvent;
use App\Event\Definition\ModifiedDefinitionActivityEvent;
use App\Event\Definition\RemovedDefinitionActivityEvent;
use App\Repository\DefinitionRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Symfony\Component\HttpFoundation\Request;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollectorInterface,
    ) {
    }

    public function createFromRequest(Request $request): Definition
    {
        $definition = DefinitionFactory::createFromRequest($request);

        $this->domainEventCollectorInterface->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function updateFromRequest(Definition $definition, Request $request): Definition
    {
        $definition = DefinitionFactory::updateFromRequest($definition, $request);

        $this->domainEventCollectorInterface->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->domainEventCollectorInterface->collect(new RemovedDefinitionActivityEvent($definition));
        $this->repository->remove($definition);
    }
}
