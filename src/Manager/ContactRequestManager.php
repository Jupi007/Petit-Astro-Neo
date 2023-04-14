<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\ContactRequest;
use App\Event\ContactRequest\CreatedContactRequestActivityEvent;
use App\Event\ContactRequest\ModifiedContactRequestActivityEvent;
use App\Event\ContactRequest\RemovedContactRequestActivityEvent;
use App\Repository\ContactRequestRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ContactRequestManager
{
    public function __construct(
        private readonly ContactRequestRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        #[Autowire('%sulu.context%')]
        private readonly string $suluContext,
        private readonly TrashManagerInterface $trashManager,
    ) {
    }

    public function create(ContactRequest $request): ContactRequest
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new CreatedContactRequestActivityEvent($request));
        }

        $this->repository->save($request, flush: true);

        return $request;
    }

    public function update(ContactRequest $request): ContactRequest
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new ModifiedContactRequestActivityEvent($request));
        }

        $this->repository->save($request, flush: true);

        return $request;
    }

    public function remove(ContactRequest $request): void
    {
        $this->trashManager->store(ContactRequest::RESOURCE_KEY, $request);

        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new RemovedContactRequestActivityEvent($request));
        }

        $this->repository->remove($request, flush: true);
    }
}
