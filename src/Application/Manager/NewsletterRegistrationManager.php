<?php

declare(strict_types=1);

namespace App\Application\Manager;

use App\Application\DTO\NewsletterRegistration\CreateNewsletterRegistrationDTO;
use App\Application\DTO\NewsletterRegistration\UpdateNewsletterRegistrationDTO;
use App\Domain\Entity\NewsletterRegistration;
use App\Domain\Event\NewsletterRegistration\CreatedNewsletterRegistrationEvent;
use App\Domain\Event\NewsletterRegistration\ModifiedNewsletterRegistrationEvent;
use App\Domain\Event\NewsletterRegistration\RemovedNewsletterRegistrationEvent;
use App\Domain\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Domain\Repository\NewsletterRegistrationRepositoryInterface;
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
