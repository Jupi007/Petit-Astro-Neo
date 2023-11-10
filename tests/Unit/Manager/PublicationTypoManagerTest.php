<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Entity\Publication;
use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\CreatedPublicationTypoEvent;
use App\Event\PublicationTypo\RemovedPublicationTypoEvent;
use App\Manager\Data\PublicationTypo\CreatePublicationTypoData;
use App\Manager\PublicationTypoManager;
use App\Repository\PublicationRepositoryInterface;
use App\Repository\PublicationTypoRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublicationTypoManagerTest extends TestCase
{
    public function testCreate(): void
    {
        $publicationId = 123;
        $description = 'description';
        $publication = $this->createMock(Publication::class);
        $data = new CreatePublicationTypoData(
            publicationId: $publicationId,
            description: $description,
        );

        $repository = $this->createMock(PublicationTypoRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(PublicationTypo::class));
        $publicationRepository = $this->createMock(PublicationRepositoryInterface::class);
        $publicationRepository
            ->expects($this->once())
            ->method('getOne')
            ->with($publicationId)
            ->willReturn($publication);
        $eventDispatcher = $this->createEventDispatcherMock(
            CreatedPublicationTypoEvent::class,
        );

        $manager = new PublicationTypoManager($repository, $publicationRepository, $eventDispatcher);
        $typo = $manager->create($data);

        $this->assertSame($description, $typo->getDescription());
        $this->assertSame($publication, $typo->getPublication());
    }

    public function testRemove(): void
    {
        $id = 123;
        $typo = $this->createMock(PublicationTypo::class);

        $repository = $this->createMock(PublicationTypoRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($typo);
        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($typo);
        $publicationRepository = $this->createMock(PublicationRepositoryInterface::class);
        $eventDispatcher = $this->createEventDispatcherMock(
            RemovedPublicationTypoEvent::class,
        );

        $manager = new PublicationTypoManager($repository, $publicationRepository, $eventDispatcher);
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
