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
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class NewsletterRegistrationManager
{
    public function __construct(
        private readonly NewsletterRegistrationRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        #[Autowire('%sulu.context%')]
        private readonly string $suluContext,
        // private readonly TrashManagerInterface $trashManager,
    ) {
    }

    public function create(NewsletterRegistration $registration): NewsletterRegistration
    {
        if (null !== $this->repository->findOneBy(['email' => $registration->getEmail()])) {
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
        // $this->trashManager->store(NewsletterRegistration::RESOURCE_KEY, $registration);
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect(new RemovedNewsletterRegistrationActivityEvent($registration));
        }

        $this->repository->remove($registration, flush: true);
    }
}
