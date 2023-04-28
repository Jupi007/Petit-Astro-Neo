<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\NewsletterRegistration;
use App\Event\NewsletterRegistration\CreatedNewsletterRegistrationActivityEvent;
use App\Event\NewsletterRegistration\ModifiedNewsletterRegistrationActivityEvent;
use App\Event\NewsletterRegistration\RemovedNewsletterRegistrationActivityEvent;
use App\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Repository\NewsletterRegistrationRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;

class NewsletterRegistrationManager
{
    public function __construct(
        private readonly NewsletterRegistrationRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly string $suluContext,
    ) {
    }

    public function create(NewsletterRegistration $registration): NewsletterRegistration
    {
        if ($this->repository->findOneBy(['email' => $registration->getEmail()]) instanceof NewsletterRegistration) {
            throw new NewsletterRegistrationEmailNotUniqueException($registration->getEmail());
        }

        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new CreatedNewsletterRegistrationActivityEvent($registration));
        }

        $this->repository->save($registration, flush: true);

        return $registration;
    }

    public function update(NewsletterRegistration $registration): NewsletterRegistration
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new ModifiedNewsletterRegistrationActivityEvent($registration));
        }

        $this->repository->save($registration, flush: true);

        return $registration;
    }

    public function remove(NewsletterRegistration $registration): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new RemovedNewsletterRegistrationActivityEvent($registration));
        }

        $this->repository->remove($registration, flush: true);
    }
}
