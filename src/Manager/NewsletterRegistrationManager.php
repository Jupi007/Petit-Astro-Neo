<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\NewsletterRegistration\CreatedNewsletterRegistrationEvent;
use App\DomainEvent\NewsletterRegistration\ModifiedNewsletterRegistrationEvent;
use App\DomainEvent\NewsletterRegistration\RemovedNewsletterRegistrationEvent;
use App\Entity\NewsletterRegistration;
use App\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Repository\NewsletterRegistrationRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsletterRegistrationManager
{
    public function __construct(
        private readonly NewsletterRegistrationRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(NewsletterRegistration $registration): NewsletterRegistration
    {
        if ($this->repository->findOneBy(['email' => $registration->getEmail()]) instanceof NewsletterRegistration) {
            throw new NewsletterRegistrationEmailNotUniqueException($registration->getEmail());
        }

        $this->eventDispatcher->dispatch(new CreatedNewsletterRegistrationEvent($registration));
        $this->repository->save($registration);

        return $registration;
    }

    public function update(NewsletterRegistration $registration): NewsletterRegistration
    {
        $this->eventDispatcher->dispatch(new ModifiedNewsletterRegistrationEvent($registration));
        $this->repository->save($registration);

        return $registration;
    }

    public function remove(NewsletterRegistration $registration): void
    {
        $this->eventDispatcher->dispatch(new RemovedNewsletterRegistrationEvent($registration));
        $this->repository->remove($registration);
    }
}
