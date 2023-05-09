<?php

declare(strict_types=1);

namespace App\Manager;

use App\ActivityEvent\Publication\CreatedPublicationActivityEvent;
use App\ActivityEvent\Publication\DraftRemovedPublicationActivityEvent;
use App\ActivityEvent\Publication\ModifiedPublicationActivityEvent;
use App\ActivityEvent\Publication\NotifiedPublicationActivityEvent;
use App\ActivityEvent\Publication\PublishedPublicationActivityEvent;
use App\ActivityEvent\Publication\RemovedPublicationActivityEvent;
use App\ActivityEvent\Publication\TranslationCopiedPublicationActivityEvent;
use App\ActivityEvent\Publication\UnpublishedPublicationActivityEvent;
use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Repository\PublicationRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentIndexer\ContentIndexerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;

class PublicationManager
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly ContentManagerInterface $contentManager,
        private readonly ContentIndexerInterface $contentIndexer,
        private readonly DomainEventCollectorInterface $domainEventCollector,
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

        $this->domainEventCollector->collect(new CreatedPublicationActivityEvent($publication));
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

        $this->domainEventCollector->collect(new ModifiedPublicationActivityEvent($publication));
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

        $this->domainEventCollector->collect(new PublishedPublicationActivityEvent($publication));
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

        $this->domainEventCollector->collect(new UnpublishedPublicationActivityEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->deindex(Publication::RESOURCE_KEY, (int) $publication->getId(), [
            ...$dimensionAttributes,
            'stage' => DimensionContentInterface::STAGE_LIVE,
        ]);
    }

    public function notify(Publication $publication): void
    {
        $publication->setNotified(true);

        $this->domainEventCollector->collect(new NotifiedPublicationActivityEvent($publication));
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

        $this->domainEventCollector->collect(new TranslationCopiedPublicationActivityEvent($publication, $srcLocale, $destLocale));
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

        $this->domainEventCollector->collect(new DraftRemovedPublicationActivityEvent($publication));
        $this->publicationRepository->save($publication);

        $this->contentIndexer->indexDimensionContent($dimensionContent);
    }

    public function remove(Publication $publication): void
    {
        if (null === $publication->getId()) {
            throw new \LogicException('You cannot remove a non-persisted publication.');
        }

        $this->domainEventCollector->collect(new RemovedPublicationActivityEvent($publication));
        $this->publicationRepository->remove($publication);

        $this->contentIndexer->deindex(Publication::RESOURCE_KEY, (int) $publication->getId());
    }
}
