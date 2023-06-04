<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\NewsletterRegistration\CreatedNewsletterRegistrationEvent;
use App\DomainEvent\NewsletterRegistration\ModifiedNewsletterRegistrationEvent;
use App\DomainEvent\NewsletterRegistration\RemovedNewsletterRegistrationEvent;
use App\DTO\NewsletterRegistration\CreateNewsletterRegistrationDTO;
use App\DTO\NewsletterRegistration\UpdateNewsletterRegistrationDTO;
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

    public function create(CreateNewsletterRegistrationDTO $dto): NewsletterRegistration
    {
        if ($this->repository->findOneBy(['email' => $dto->email]) instanceof NewsletterRegistration) {
            throw new NewsletterRegistrationEmailNotUniqueException($dto->email);
        }

        $registration = new NewsletterRegistration(
            $dto->email,
            $dto->locale,
        );

        $this->eventDispatcher->dispatch(new CreatedNewsletterRegistrationEvent($registration));
        $this->repository->save($registration);

        return $registration;
    }

    public function update(UpdateNewsletterRegistrationDTO $dto): NewsletterRegistration
    {
        $registration = $this->repository->getOne($dto->id)
            ->setLocale($dto->locale);

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
