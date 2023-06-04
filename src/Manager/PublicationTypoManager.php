<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\PublicationTypo\CreatedPublicationTypoEvent;
use App\DomainEvent\PublicationTypo\RemovedPublicationTypoEvent;
use App\DTO\PublicationTypo\CreatePublicationTypoDTO;
use App\Entity\PublicationTypo;
use App\Repository\PublicationRepositoryInterface;
use App\Repository\PublicationTypoRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepositoryInterface $repository,
        private readonly PublicationRepositoryInterface $publicationRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(CreatePublicationTypoDTO $dto): void
    {
        $publication = $this->publicationRepository->getOne($dto->publicationId);

        $typo = new PublicationTypo(
            description: $dto->description,
            publication: $publication,
        );

        $this->eventDispatcher->dispatch(new CreatedPublicationTypoEvent($typo));
        $this->repository->save($typo);
    }

    public function remove(int $id): void
    {
        $typo = $this->repository->getOne($id);

        $this->eventDispatcher->dispatch(new RemovedPublicationTypoEvent($typo));
        $this->repository->remove($typo);
    }
}
