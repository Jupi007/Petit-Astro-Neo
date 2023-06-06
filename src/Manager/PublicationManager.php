<?php

declare(strict_types=1);

namespace App\Manager;

use App\DomainEvent\Publication\CreatedPublicationEvent;
use App\DomainEvent\Publication\DraftRemovedPublicationEvent;
use App\DomainEvent\Publication\ModifiedPublicationEvent;
use App\DomainEvent\Publication\NotifiedPublicationEvent;
use App\DomainEvent\Publication\PublishedPublicationEvent;
use App\DomainEvent\Publication\RemovedPublicationEvent;
use App\DomainEvent\Publication\TranslationCopiedPublicationEvent;
use App\DomainEvent\Publication\UnpublishedPublicationEvent;
use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Exception\PublicationAlreadyNotifiedException;
use App\Repository\PublicationRepositoryInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentIndexer\ContentIndexerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublicationManager
{
    public function __construct(
        private readonly PublicationRepositoryInterface $repository,
        private readonly ContentManagerInterface $contentManager,
        private readonly ContentIndexerInterface $contentIndexer,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data, string $locale): Publication
    {
        $publication = new Publication();
        $dimensionContent = $this->contentManager->persist(
            $publication,
            $data,
            ['locale' => $locale],
        );

        $this->eventDispatcher->dispatch(new CreatedPublicationEvent($publication));
        $this->repository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);

        return $publication;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data, string $locale): Publication
    {
        $publication = $this->repository->getOne($id);

        /** @var PublicationDimensionContent $dimensionContent */
        $dimensionContent = $this->contentManager->persist(
            $publication,
            $data,
            ['locale' => $locale],
        );

        if (WorkflowInterface::WORKFLOW_PLACE_PUBLISHED === $dimensionContent->getWorkflowPlace()) {
            $dimensionContent = $this->contentManager->applyTransition(
                $publication,
                ['locale' => $locale],
                WorkflowInterface::WORKFLOW_TRANSITION_CREATE_DRAFT,
            );
        }

        $this->eventDispatcher->dispatch(new ModifiedPublicationEvent($publication));
        $this->repository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);

        return $publication;
    }

    public function publish(int $id, string $locale): Publication
    {
        $publication = $this->repository->getOne($id);

        $this->contentManager->applyTransition(
            $publication,
            ['locale' => $locale],
            WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
        );

        $this->eventDispatcher->dispatch(new PublishedPublicationEvent($publication));
        $this->repository->save($publication);

        $this->contentIndexer->index($publication, [
            'locale' => $locale,
            'stage' => DimensionContentInterface::STAGE_LIVE,
        ]);

        return $publication;
    }

    public function unpublish(int $id, string $locale): Publication
    {
        $publication = $this->repository->getOne($id);

        if (null === $publication->getId()) {
            throw new \LogicException('You cannot unpublish a non-persisted publication.');
        }

        $this->contentManager->applyTransition(
            $publication,
            ['locale' => $locale],
            WorkflowInterface::WORKFLOW_TRANSITION_UNPUBLISH,
        );

        $this->eventDispatcher->dispatch(new UnpublishedPublicationEvent($publication));
        $this->repository->save($publication);

        $this->contentIndexer->deindex(Publication::RESOURCE_KEY, (int) $publication->getId(), [
            'locale' => $locale,
            'stage' => DimensionContentInterface::STAGE_LIVE,
        ]);

        return $publication;
    }

    public function notify(int $id): Publication
    {
        $publication = $this->repository->getOne($id);

        if ($publication->isNotified()) {
            throw new PublicationAlreadyNotifiedException();
        }

        $publication->setNotified(true);

        $this->eventDispatcher->dispatch(new NotifiedPublicationEvent($publication));
        $this->repository->save($publication);

        return $publication;
    }

    public function copyLocale(int $id, string $srcLocale, string $destLocale): Publication
    {
        $publication = $this->repository->getOne($id);

        $this->contentManager->copy(
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

        $this->eventDispatcher->dispatch(new TranslationCopiedPublicationEvent($publication, $srcLocale, $destLocale));
        $this->repository->save($publication);

        return $publication;
    }

    public function removeDraft(int $id, string $locale): Publication
    {
        $publication = $this->repository->getOne($id);

        $dimensionContent = $this->contentManager->applyTransition(
            $publication,
            ['locale' => $locale],
            WorkflowInterface::WORKFLOW_TRANSITION_REMOVE_DRAFT,
        );

        $this->eventDispatcher->dispatch(new DraftRemovedPublicationEvent($publication));
        $this->repository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);

        return $publication;
    }

    public function remove(int $id): Publication
    {
        $publication = $this->repository->getOne($id);

        if (null === $publication->getId()) {
            throw new \LogicException('You cannot remove a non-persisted publication.');
        }

        $this->eventDispatcher->dispatch(new RemovedPublicationEvent($publication));
        $this->repository->remove($publication);

        $this->contentIndexer->deindex(Publication::RESOURCE_KEY, (int) $publication->getId());

        return $publication;
    }
}
