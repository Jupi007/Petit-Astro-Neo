<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\CreatedPublicationTypoActivityEvent;
use App\Event\PublicationTypo\RemovedPublicationTypoActivityEvent;
use App\Repository\PublicationTypoRepositoryInterface;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepositoryInterface $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly string $suluContext,
    ) {
    }

    public function create(PublicationTypo $typo): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new CreatedPublicationTypoActivityEvent($typo));
        }

        $this->repository->save($typo);
    }

    public function remove(PublicationTypo $typo): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new RemovedPublicationTypoActivityEvent($typo));
        }

        $this->repository->remove($typo);
    }
}
