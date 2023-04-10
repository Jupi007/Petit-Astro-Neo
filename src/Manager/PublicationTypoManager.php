<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\RemovedPublicationTypoActivityEvent;
use App\Repository\PublicationTypoRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly TrashManagerInterface $trashManager,
    ) {
    }

    public function create(PublicationTypo $typo): void
    {
        $this->repository->save($typo, flush: true);
    }

    public function remove(PublicationTypo $typo): void
    {
        $this->trashManager->store(PublicationTypo::RESOURCE_KEY, $typo);
        $this->domainEventCollector->collect(new RemovedPublicationTypoActivityEvent($typo));
        $this->repository->remove($typo, flush: true);
    }
}
