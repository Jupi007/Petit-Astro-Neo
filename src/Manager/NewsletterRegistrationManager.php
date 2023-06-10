<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\NewsletterRegistration;
use App\Event\NewsletterRegistration\CreatedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\ModifiedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\RemovedNewsletterRegistrationEvent;
use App\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Manager\Data\NewsletterRegistration\CreateNewsletterRegistrationData;
use App\Manager\Data\NewsletterRegistration\UpdateNewsletterRegistrationData;
use App\Repository\NewsletterRegistrationRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsletterRegistrationManager
{
    public function __construct(
        private readonly NewsletterRegistrationRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(CreateNewsletterRegistrationData $data): NewsletterRegistration
    {
        if ($this->repository->findOneBy(['email' => $data->email]) instanceof NewsletterRegistration) {
            throw new NewsletterRegistrationEmailNotUniqueException($data->email);
        }

        $registration = new NewsletterRegistration(
            $data->email,
            $data->locale,
        );

        $this->eventDispatcher->dispatch(new CreatedNewsletterRegistrationEvent($registration));
        $this->repository->save($registration);

        return $registration;
    }

    public function update(UpdateNewsletterRegistrationData $data): NewsletterRegistration
    {
        $registration = $this->repository->getOne($data->id)
            ->setLocale($data->locale);

        $this->eventDispatcher->dispatch(new ModifiedNewsletterRegistrationEvent($registration));
        $this->repository->save($registration);

        return $registration;
    }

    public function remove(int $id): void
    {
        $registration = $this->repository->getOne($id);

        $this->eventDispatcher->dispatch(new RemovedNewsletterRegistrationEvent($registration));
        $this->repository->remove($registration);
    }
}
