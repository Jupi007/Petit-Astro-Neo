<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepositoryInterface;
use App\SuluDomainEvent\ContactRequest\CreatedContactRequestActivityEvent;
use App\SuluDomainEvent\ContactRequest\ModifiedContactRequestActivityEvent;
use App\SuluDomainEvent\ContactRequest\RemovedContactRequestActivityEvent;
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
