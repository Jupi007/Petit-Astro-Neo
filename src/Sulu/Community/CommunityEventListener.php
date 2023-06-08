<?php

declare(strict_types=1);

namespace App\Sulu\Community;

use App\DTO\NewsletterRegistration\CreateNewsletterRegistrationDTO;
use App\DTO\NewsletterRegistration\UpdateNewsletterRegistrationDTO;
use App\Entity\NewsletterRegistration;
use App\Exception\NullAssertionException;
use App\Manager\NewsletterRegistrationManager;
use App\Repository\NewsletterRegistrationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\CommunityBundle\Event\AbstractCommunityEvent;
use Sulu\Bundle\CommunityBundle\Event\UserConfirmedEvent;
use Sulu\Bundle\CommunityBundle\Event\UserEmailConfirmedEvent;
use Sulu\Bundle\CommunityBundle\Event\UserProfileSavedEvent;
use Sulu\Bundle\SecurityBundle\Domain\Event\UserCreatedEvent;
use Sulu\Bundle\SecurityBundle\Domain\Event\UserModifiedEvent;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommunityEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly NewsletterRegistrationRepositoryInterface $repository,
        private readonly NewsletterRegistrationManager $manager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Community event
            UserConfirmedEvent::class => 'onCommunityEvent',
            UserProfileSavedEvent::class => 'onCommunityEvent',
            UserEmailConfirmedEvent::class => 'onCommunityEvent',
            // Sulu security admin events
            UserCreatedEvent::class => 'onAdminEvent',
            UserModifiedEvent::class => 'onAdminEvent',
        ];
    }

    public function onAdminEvent(UserCreatedEvent|UserModifiedEvent $event): void
    {
        $user = $event->getResourceUser();
        $this->onUserEvent($user);

        // Needed because domain events are dispatched after flush.
        $this->entityManager->flush();
    }

    public function onCommunityEvent(AbstractCommunityEvent $event): void
    {
        $user = $event->getUser();

        $registration = $this->onUserEvent($user);

        if (null === $registration?->getId()) {
            // The user isn't logged in yet, so we set this manually.
            $registration?->setCreator($user);
        }
    }

    private function onUserEvent(UserInterface $user): ?NewsletterRegistration
    {
        if (!$user instanceof User || null === $user->getEmail()) {
            return null;
        }

        $registration = $this->repository->findOneBy(['email' => $user->getContact()->getMainEmail()]);

        if ($registration instanceof NewsletterRegistration) {
            if (!$user->getContact()->getNewsletter()) {
                $this->removeRegistration((int) $registration->getId());
            } else {
                $this->updateRegistration((int) $registration->getId(), $user);
            }
        } elseif ($user->getContact()->getNewsletter()) {
            $registration = $this->createRegistration($user);
        }

        return $registration;
    }

    private function createRegistration(User $user): NewsletterRegistration
    {
        $user->getContact()->setNewsletter(true);
        $dto = new CreateNewsletterRegistrationDTO(
            email: $user->getContact()->getMainEmail() ?? throw new NullAssertionException(),
            locale: $user->getLocale(),
        );

        return $this->manager->create($dto);
    }

    private function updateRegistration(int $id, User $user): void
    {
        $user->getContact()->setNewsletter(true);
        $dto = new UpdateNewsletterRegistrationDTO(
            $id,
            $user->getLocale(),
        );
        $this->manager->update($dto);
    }

    private function removeRegistration(int $id): void
    {
        $this->manager->remove($id);
    }
}
