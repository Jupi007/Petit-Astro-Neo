<?php

declare(strict_types=1);

namespace App\Manager;

use App\ActivityEvent\ContactRequest\CreatedContactRequestActivityEvent;
use App\ActivityEvent\ContactRequest\ModifiedContactRequestActivityEvent;
use App\ActivityEvent\ContactRequest\RemovedContactRequestActivityEvent;
use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepositoryInterface;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;

class ContactRequestManager
{
    public function __construct(
        private readonly ContactRequestRepositoryInterface $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly string $suluContext,
    ) {
    }

    public function create(ContactRequest $request): ContactRequest
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new CreatedContactRequestActivityEvent($request));
        }

        $this->repository->save($request);

        return $request;
    }

    public function update(ContactRequest $request): ContactRequest
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new ModifiedContactRequestActivityEvent($request));
        }

        $this->repository->save($request);

        return $request;
    }

    public function remove(ContactRequest $request): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new RemovedContactRequestActivityEvent($request));
        }

        $this->repository->remove($request);
    }
}
