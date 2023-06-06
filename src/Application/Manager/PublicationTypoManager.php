<?php

declare(strict_types=1);

namespace App\Application\Manager;

use App\Application\DTO\PublicationTypo\CreatePublicationTypoDTO;
use App\Domain\Entity\PublicationTypo;
use App\Domain\Event\PublicationTypo\CreatedPublicationTypoEvent;
use App\Domain\Event\PublicationTypo\RemovedPublicationTypoEvent;
use App\Domain\Repository\PublicationRepositoryInterface;
use App\Domain\Repository\PublicationTypoRepositoryInterface;
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
