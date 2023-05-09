<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\ContactRequest\CreatedContactRequestEvent;
use App\DomainEvent\ContactRequest\ModifiedContactRequestEvent;
use App\DomainEvent\ContactRequest\RemovedContactRequestEvent;
use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContactRequestManager
{
    public function __construct(
        private readonly ContactRequestRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(ContactRequest $request): ContactRequest
    {
        $this->eventDispatcher->dispatch(new CreatedContactRequestEvent($request));
        $this->repository->save($request);

        return $request;
    }

    public function update(ContactRequest $request): ContactRequest
    {
        $this->eventDispatcher->dispatch(new ModifiedContactRequestEvent($request));
        $this->repository->save($request);

        return $request;
    }

    public function remove(ContactRequest $request): void
    {
        $this->eventDispatcher->dispatch(new RemovedContactRequestEvent($request));
        $this->repository->remove($request);
    }
}
