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
use App\Repository\PublicationRepository;
use Sulu\Bundle\ContentBundle\Content\Application\ContentIndexer\ContentIndexerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublicationManager
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly ContentManagerInterface $contentManager,
        private readonly ContentIndexerInterface $contentIndexer,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $dimensionAttributes
     */
    public function create(array $data, array $dimensionAttributes): Publication
    {
        $publication = new Publication();
        $dimensionContent = $this->contentManager->persist($publication, $data, $dimensionAttributes);

        $this->eventDispatcher->dispatch(new CreatedPublicationEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);

        return $publication;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $dimensionAttributes
     */
    public function update(Publication $publication, array $data, array $dimensionAttributes): void
    {
        /** @var PublicationDimensionContent $dimensionContent */
        $dimensionContent = $this->contentManager->persist($publication, $data, $dimensionAttributes);

        if (WorkflowInterface::WORKFLOW_PLACE_PUBLISHED === $dimensionContent->getWorkflowPlace()) {
            $dimensionContent = $this->contentManager->applyTransition(
                $publication,
                $dimensionAttributes,
                WorkflowInterface::WORKFLOW_TRANSITION_CREATE_DRAFT,
            );
        }

        $this->eventDispatcher->dispatch(new ModifiedPublicationEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function publish(Publication $publication, array $dimensionAttributes): void
    {
        $this->contentManager->applyTransition(
            $publication,
            $dimensionAttributes,
            WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
        );

        $this->eventDispatcher->dispatch(new PublishedPublicationEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->index($publication, [
            ...$dimensionAttributes,
            'stage' => DimensionContentInterface::STAGE_LIVE,
        ]);
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function unpublish(Publication $publication, array $dimensionAttributes): void
    {
        if (null === $publication->getId()) {
            throw new \LogicException('You cannot unpublish a non-persisted publication.');
        }

        $this->contentManager->applyTransition(
            $publication,
            $dimensionAttributes,
            WorkflowInterface::WORKFLOW_TRANSITION_UNPUBLISH,
        );

        $this->eventDispatcher->dispatch(new UnpublishedPublicationEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->deindex(Publication::RESOURCE_KEY, (int) $publication->getId(), [
            ...$dimensionAttributes,
            'stage' => DimensionContentInterface::STAGE_LIVE,
        ]);
    }

    public function notify(Publication $publication): void
    {
        if ($publication->isNotified()) {
            throw new PublicationAlreadyNotifiedException();
        }

        // $publication->setNotified(true);

        $this->eventDispatcher->dispatch(new NotifiedPublicationEvent($publication));
        $this->publicationRepository->save($publication);
    }

    public function copyLocale(Publication $publication, string $srcLocale, string $destLocale): void
    {
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
        $this->publicationRepository->save($publication);
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function removeDraft(Publication $publication, array $dimensionAttributes): void
    {
        $dimensionContent = $this->contentManager->applyTransition(
            $publication,
            $dimensionAttributes,
            WorkflowInterface::WORKFLOW_TRANSITION_REMOVE_DRAFT,
        );

        $this->eventDispatcher->dispatch(new DraftRemovedPublicationEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);
    }

    public function remove(Publication $publication): void
    {
        if (null === $publication->getId()) {
            throw new \LogicException('You cannot remove a non-persisted publication.');
        }

        $this->eventDispatcher->dispatch(new RemovedPublicationEvent($publication));
        $this->publicationRepository->remove($publication);

        $this->contentIndexer->deindex(Publication::RESOURCE_KEY, (int) $publication->getId());
    }
}
