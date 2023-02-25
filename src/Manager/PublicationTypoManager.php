<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\RemovedPublicationTypoActivityEvent;
use App\Repository\PublicationTypoRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
    ) {
    }

    public function create(PublicationTypo $typo): void
    {
        $this->repository->save($typo);
    }

    public function remove(PublicationTypo $typo): void
    {
        $this->domainEventCollector->collect(new RemovedPublicationTypoActivityEvent($typo));
        $this->repository->remove($typo);
    }
}
