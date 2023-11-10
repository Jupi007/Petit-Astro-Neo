<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Entity\NewsletterRegistration;
use App\Event\NewsletterRegistration\CreatedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\ModifiedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\RemovedNewsletterRegistrationEvent;
use App\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Manager\Data\NewsletterRegistration\CreateNewsletterRegistrationData;
use App\Manager\Data\NewsletterRegistration\UpdateNewsletterRegistrationData;
use App\Manager\NewsletterRegistrationManager;
use App\Repository\NewsletterRegistrationRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsletterManagerTest extends TestCase
{
    public function testCreate(): void
    {
        $locale = 'fr';
        $email = 'test@test.fr';
        $data = new CreateNewsletterRegistrationData(
            email: $email,
            locale: $locale,
        );

        $repository = $this->createMock(NewsletterRegistrationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(NewsletterRegistration::class));
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn(null);
        $eventDispatcher = $this->createEventDispatcherMock(
            CreatedNewsletterRegistrationEvent::class,
        );

        $manager = new NewsletterRegistrationManager($repository, $eventDispatcher);
        $registration = $manager->create($data);

        $this->assertSame($locale, $registration->getLocale());
        $this->assertSame($email, $registration->getEmail());
    }

    public function testCreateWithNonUniqueEmail(): void
    {
        $locale = 'fr';
        $email = 'test@test.fr';
        $data = new CreateNewsletterRegistrationData(
            email: $email,
            locale: $locale,
        );

        $repository = $this->createMock(NewsletterRegistrationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn($this->createMock(NewsletterRegistration::class));
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->expectException(NewsletterRegistrationEmailNotUniqueException::class);
        (new NewsletterRegistrationManager($repository, $eventDispatcher))->create($data);
    }

    public function testUpdate(): void
    {
        $id = 123;
        $oldLocale = 'en';
        $newLocale = 'en';
        $email = 'test@test.fr';
        $data = new UpdateNewsletterRegistrationData(
            id: $id,
            locale: $newLocale,
        );

        $registration = new NewsletterRegistration($email, $oldLocale);

        $repository = $this->createMock(NewsletterRegistrationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($registration);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(NewsletterRegistration::class));
        $eventDispatcher = $this->createEventDispatcherMock(
            ModifiedNewsletterRegistrationEvent::class,
        );

        $manager = new NewsletterRegistrationManager($repository, $eventDispatcher);
        $updatedRegistration = $manager->update($data);

        $this->assertSame($newLocale, $updatedRegistration->getLocale());
    }

    public function testRemove(): void
    {
        $id = 123;
        $registration = $this->createMock(NewsletterRegistration::class);

        $repository = $this->createMock(NewsletterRegistrationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($registration);
        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($registration);
        $eventDispatcher = $this->createEventDispatcherMock(
            RemovedNewsletterRegistrationEvent::class,
        );

        $manager = new NewsletterRegistrationManager($repository, $eventDispatcher);
        $manager->remove($id);
    }

    /** @param class-string $eventType */
    private function createEventDispatcherMock(string $eventType): EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf($eventType));

        return $eventDispatcher;
    }
}
