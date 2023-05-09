<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\PublicationTypo\CreatedPublicationTypoEvent;
use App\DomainEvent\PublicationTypo\RemovedPublicationTypoEvent;
use App\Entity\PublicationTypo;
use App\Repository\PublicationTypoRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(PublicationTypo $typo): void
    {
        $this->eventDispatcher->dispatch(new CreatedPublicationTypoEvent($typo));
        $this->repository->save($typo);
    }

    public function remove(PublicationTypo $typo): void
    {
        $this->eventDispatcher->dispatch(new RemovedPublicationTypoEvent($typo));
        $this->repository->remove($typo);
    }
}
