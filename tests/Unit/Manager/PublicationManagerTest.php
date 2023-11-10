<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Event\Publication\CreatedPublicationEvent;
use App\Event\Publication\DraftRemovedPublicationEvent;
use App\Event\Publication\ModifiedPublicationEvent;
use App\Event\Publication\NotifiedPublicationEvent;
use App\Event\Publication\PublishedPublicationEvent;
use App\Event\Publication\RemovedPublicationEvent;
use App\Event\Publication\TranslationCopiedPublicationEvent;
use App\Event\Publication\UnpublishedPublicationEvent;
use App\Exception\PublicationAlreadyNotifiedException;
use App\Manager\PublicationManager;
use App\Repository\PublicationRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Sulu\Bundle\ContentBundle\Content\Application\ContentIndexer\ContentIndexerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublicationManagerTest extends TestCase
{
    public function testCreate(): void
    {
        $data = [
            'title' => 'title',
        ];
        $locale = 'fr';

        $dimensionContent = $this->createMock(PublicationDimensionContent::class);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Publication::class));

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->isInstanceOf(Publication::class),
                $data,
                ['locale' => $locale],
            )
            ->willReturn($dimensionContent);

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $contentIndexer
            ->expects($this->once())
            ->method('indexDimensionContent')
            ->with($dimensionContent);

        $eventDispatcher = $this->createEventDispatcherMock(CreatedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->create($data, $locale);
    }

    /** @testWith([true, false]) */
    public function testUpdate(bool $published = false): void
    {
        $id = 123;
        $data = [
            'title' => 'title',
        ];
        $locale = 'fr';

        $publication = $this->createMock(Publication::class);

        $dimensionContent = $this->createMock(PublicationDimensionContent::class);
        $dimensionContent
            ->expects($this->once())
            ->method('getWorkflowPlace')
            ->willReturn(
                $published
                ? WorkflowInterface::WORKFLOW_PLACE_PUBLISHED
                : WorkflowInterface::WORKFLOW_PLACE_DRAFT,
            );

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Publication::class));

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->isInstanceOf(Publication::class),
                $data,
                ['locale' => $locale],
            )
            ->willReturn($dimensionContent);
        if ($published) {
            $contentManager
                ->expects($this->once())
                ->method('applyTransition')
                ->with(
                    $publication,
                    ['locale' => $locale],
                    WorkflowInterface::WORKFLOW_TRANSITION_CREATE_DRAFT,
                )
                ->willReturn($dimensionContent);
        }

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $contentIndexer
            ->expects($this->once())
            ->method('indexDimensionContent')
            ->with($dimensionContent);

        $eventDispatcher = $this->createEventDispatcherMock(ModifiedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->update($id, $data, $locale);
    }

    public function testPublish(): void
    {
        $id = 123;
        $locale = 'fr';
        $publication = $this->createMock(Publication::class);
        $dimensionContent = $this->createMock(PublicationDimensionContent::class);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager
                ->expects($this->once())
                ->method('applyTransition')
                ->with(
                    $publication,
                    ['locale' => $locale],
                    WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
                )
                ->willReturn($dimensionContent);

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $contentIndexer
            ->expects($this->once())
            ->method('index')
            ->with($publication, [
                'locale' => $locale,
                'stage' => DimensionContentInterface::STAGE_LIVE,
            ]);

        $eventDispatcher = $this->createEventDispatcherMock(PublishedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->publish($id, $locale);
    }

    public function testUnpublish(): void
    {
        $id = 123;
        $locale = 'fr';

        $publication = $this->createMock(Publication::class);
        $publication
            ->expects($this->once())
            ->method('getId')
            ->willReturn($id);

        $dimensionContent = $this->createMock(PublicationDimensionContent::class);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager
                ->expects($this->once())
                ->method('applyTransition')
                ->with(
                    $publication,
                    ['locale' => $locale],
                    WorkflowInterface::WORKFLOW_TRANSITION_UNPUBLISH,
                )
                ->willReturn($dimensionContent);

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $contentIndexer
            ->expects($this->once())
            ->method('deindex')
            ->with(Publication::RESOURCE_KEY, $id, [
                'locale' => $locale,
                'stage' => DimensionContentInterface::STAGE_LIVE,
            ]);

        $eventDispatcher = $this->createEventDispatcherMock(UnpublishedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->unpublish($id, $locale);
    }

    public function testNotify(): void
    {
        $id = 123;

        $publication = $this->createMock(Publication::class);
        $publication
            ->expects($this->once())
            ->method('isNotified')
            ->willReturn(false);
        $publication
            ->expects($this->once())
            ->method('setNotified')
            ->with(true);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $eventDispatcher = $this->createEventDispatcherMock(NotifiedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->notify($id);
    }

    public function testNotifyAlreadyNotified(): void
    {
        $id = 123;

        $publication = $this->createMock(Publication::class);
        $publication
            ->expects($this->once())
            ->method('isNotified')
            ->willReturn(true);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->expectException(PublicationAlreadyNotifiedException::class);
        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->notify($id);
    }

    public function testCopyLocale(): void
    {
        $id = 123;
        $srcLocale = 'fr';
        $destLocale = 'en';

        $publication = $this->createMock(Publication::class);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager
            ->expects($this->once())
            ->method('copy')
            ->with(
                $publication,
                [
                    'stage' => DimensionContentInterface::STAGE_DRAFT,
                    'locale' => $srcLocale,
                ],
                $publication,
                [
                    'stage' => DimensionContentInterface::STAGE_DRAFT,
                    'locale' => $destLocale,
                ],
            );

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $eventDispatcher = $this->createEventDispatcherMock(TranslationCopiedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->copyLocale($id, $srcLocale, $destLocale);
    }

    public function testRemoveDraft(): void
    {
        $id = 123;
        $locale = 'fr';

        $publication = $this->createMock(Publication::class);

        $dimensionContent = $this->createMock(PublicationDimensionContent::class);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager
            ->expects($this->once())
            ->method('applyTransition')
            ->with(
                $publication,
                ['locale' => $locale],
                WorkflowInterface::WORKFLOW_TRANSITION_REMOVE_DRAFT,
            )
            ->willReturn($dimensionContent);

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $contentIndexer
            ->expects($this->once())
            ->method('indexDimensionContent')
            ->with($dimensionContent);

        $eventDispatcher = $this->createEventDispatcherMock(DraftRemovedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
        $manager->removeDraft($id, $locale);
    }

    public function testRemove(): void
    {
        $id = 123;

        $publication = $this->createMock(Publication::class);
        $publication
            ->expects($this->once())
            ->method('getId')
            ->willReturn($id);

        $repository = $this->createMock(PublicationRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($publication);
        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($publication);

        $contentManager = $this->createMock(ContentManagerInterface::class);

        $contentIndexer = $this->createMock(ContentIndexerInterface::class);
        $contentIndexer
            ->expects($this->once())
            ->method('deindex')
            ->with(Publication::RESOURCE_KEY, $id);

        $eventDispatcher = $this->createEventDispatcherMock(RemovedPublicationEvent::class);

        $manager = new PublicationManager($repository, $contentManager, $contentIndexer, $eventDispatcher);
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
