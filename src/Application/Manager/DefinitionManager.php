<?php

declare(strict_types=1);

namespace App\Application\Manager;

use App\Application\DTO\Definition\CreateDefinitionDTO;
use App\Application\DTO\Definition\UpdateDefinitionDTO;
use App\Domain\Entity\Definition;
use App\Domain\Event\Definition\CreatedDefinitionEvent;
use App\Domain\Event\Definition\ModifiedDefinitionEvent;
use App\Domain\Event\Definition\RemovedDefinitionEvent;
use App\Domain\Event\Definition\TranslationCopiedDefinitionEvent;
use App\Domain\Repository\DefinitionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(CreateDefinitionDTO $dto): Definition
    {
        $definition = (new Definition())
            ->setLocale($dto->locale)
            ->setTitle($dto->title)
            ->setDescription($dto->description)
            ->setRoutePath($dto->routePath);

        // Save a first time to generate an ID in database.
        $this->repository->save($definition);

        $this->eventDispatcher->dispatch(new CreatedDefinitionEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function update(UpdateDefinitionDTO $dto): Definition
    {
        $definition = $this->repository->getOne($dto->id)
            ->setLocale($dto->locale)
            ->setTitle($dto->title)
            ->setDescription($dto->description)
            ->setRoutePath($dto->routePath);

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
