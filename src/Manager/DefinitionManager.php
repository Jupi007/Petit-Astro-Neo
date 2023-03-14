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

/** @phpstan-type DefinitionData array{
 *      title: string|null,
 *      description: string|null,
 * } */
class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly TrashManagerInterface $trashManager,
    ) {
    }

    /** @param DefinitionData $data */
    public function create(array $data, string $locale): Definition
    {
        $definition = new Definition();

        $this->mapDataToDefinition($definition, $data, $locale);
        $this->domainEventCollector->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    /** @param DefinitionData $data */
    public function update(Definition $definition, array $data, string $locale): Definition
    {
        $this->mapDataToDefinition($definition, $data, $locale);
        $this->domainEventCollector->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->trashManager->store(Definition::RESOURCE_KEY, $definition);
        $this->domainEventCollector->collect(new RemovedDefinitionActivityEvent($definition));
        $this->repository->remove($definition);
    }

    /** @param DefinitionData $data */
    private function mapDataToDefinition(Definition $definition, array $data, string $locale): void
    {
        $definition
            ->setLocale($locale)
            ->setTitle($data['title'] ?? '')
            ->setDescription($data['description'] ?? '');
    }
}
