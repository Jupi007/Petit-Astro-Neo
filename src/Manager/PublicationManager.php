<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Repository\PublicationRepository;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;

class PublicationManager
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly ContentManagerInterface $contentManager,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $dimensionAttributes
     */
    public function create(array $data, array $dimensionAttributes): Publication
    {
        $publication = new Publication();

        $this->contentManager->persist($publication, $data, $dimensionAttributes);
        $this->publicationRepository->save($publication);

        // Index draft dimension content
        // $this->contentIndexer->indexDimensionContent($dimensionContent);

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

        $this->publicationRepository->save($publication);

        // Index draft dimension content
        // $this->contentIndexer->indexDimensionContent($dimensionContent);
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function resolve(Publication $publication, array $dimensionAttributes): DimensionContentInterface
    {
        return $this->contentManager->resolve($publication, $dimensionAttributes);
    }

    /** @return mixed[] */
    public function normalize(DimensionContentInterface $dimensionContent): array
    {
        return $this->contentManager->normalize($dimensionContent);
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function publish(Publication $publication, array $dimensionAttributes): void
    {
        $this->contentManager->applyTransition(
            $publication,
            $dimensionAttributes,
            WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
        );

        $this->publicationRepository->save($publication);

        // Index live dimension content
        // $this->contentIndexer->index($publication, \array_merge($dimensionAttributes, [
            //     'stage' => DimensionContentInterface::STAGE_LIVE,
        // ]));
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function unpublish(Publication $publication, array $dimensionAttributes): void
    {
        $this->contentManager->applyTransition(
            $publication,
            $dimensionAttributes,
            WorkflowInterface::WORKFLOW_TRANSITION_UNPUBLISH,
        );

        $this->publicationRepository->save($publication);

        // Deindex live dimension content
        // $this->contentIndexer->deindex(Publication::RESOURCE_KEY, $id, \array_merge(
        //     $dimensionAttributes,
        //     ['stage' => DimensionContentInterface::STAGE_LIVE],
        // ));
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

        $this->publicationRepository->save($publication);
    }

    /** @param array<string, mixed> $dimensionAttributes */
    public function removeDraft(Publication $publication, array $dimensionAttributes): void
    {
        $this->contentManager->applyTransition(
            $publication,
            $dimensionAttributes,
            WorkflowInterface::WORKFLOW_TRANSITION_REMOVE_DRAFT,
        );

        $this->publicationRepository->save($publication);

        // Index draft dimension content
        // $this->contentIndexer->indexDimensionContent($dimensionContent);
    }

    public function remove(Publication $publication): void
    {
        $this->publicationRepository->remove($publication);

        // Remove all documents with given id from index
        // $this->contentIndexer->deindex(Publication::RESOURCE_KEY, $id);
    }
}
