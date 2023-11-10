<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Definition;
use App\Event\Definition\CreatedDefinitionEvent;
use App\Event\Definition\ModifiedDefinitionEvent;
use App\Event\Definition\RemovedDefinitionEvent;
use App\Event\Definition\TranslationCopiedDefinitionEvent;
use App\Manager\Data\Definition\CreateDefinitionData;
use App\Manager\Data\Definition\UpdateDefinitionData;
use App\Repository\DefinitionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(CreateDefinitionData $data): Definition
    {
        $definition = (new Definition())
            ->setLocale($data->locale)
            ->setTitle($data->title)
            ->setDescription($data->description)
            ->setRoutePath($data->routePath);

        // Save a first time to generate an ID in database.
        $this->repository->save($definition);

        $this->eventDispatcher->dispatch(new CreatedDefinitionEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function update(UpdateDefinitionData $data): Definition
    {
        $definition = $this->repository->getOneLocalized($data->id, $data->locale)
            ->setTitle($data->title)
            ->setDescription($data->description)
            ->setRoutePath($data->routePath);

        $this->eventDispatcher->dispatch(new ModifiedDefinitionEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function copyLocale(int $id, string $srcLocale, string $destLocale): Definition
    {
        $definition = $this->repository->getOneLocalized($id, $srcLocale);

        $srcTitle = $definition->getTitle() ?? '';
        $srcDescription = $definition->getDescription() ?? '';
        $srcRoutePath = $definition->getRoutePath() ?? '';

        $definition
            ->setLocale($destLocale)
            ->setTitle($srcTitle)
            ->setDescription($srcDescription)
            ->setRoutePath($srcRoutePath);

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
        $this->repository->remove($definition);
    }
}
