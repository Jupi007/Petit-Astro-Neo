<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\CreatedPublicationTypoActivityEvent;
use App\Event\PublicationTypo\RemovedPublicationTypoActivityEvent;
use App\Repository\PublicationTypoRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly string $suluContext,
    ) {
    }

    public function create(PublicationTypo $typo): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new CreatedPublicationTypoActivityEvent($typo));
        }

        $this->repository->save($typo, flush: true);
    }

    public function remove(PublicationTypo $typo): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new RemovedPublicationTypoActivityEvent($typo));
        }

        $this->repository->remove($typo, flush: true);
    }
}
