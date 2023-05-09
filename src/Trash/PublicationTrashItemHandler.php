<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\PublicationAdmin;
use App\DomainEvent\Publication\RestoredPublicationEvent;
use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Entity\PublicationTypo;
use Sulu\Bundle\CategoryBundle\Entity\CategoryRepositoryInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Sulu\Bundle\TagBundle\Tag\TagRepositoryInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-type TrashData array{
 *   dimensionContents: DimensionContentTrashData,
 *   typos: TypoTrashData,
 *   notified: bool,
 * }
 * @phpstan-type DimensionContentTrashData array{
 *   creatorId: int|null,
 *   created: string,
 *   changerId: int|null,
 *   changed: string,
 *   locale: string|null,
 *   ghostLocale: string|null,
 *   availableLocales: string[]|null,
 *   mainWebspace: string|null,
 *   templateKey: string|null,
 *   templateData: mixed[],
 *   seoTitle: string|null,
 *   seoDescription: string|null,
 *   seoKeywords: string|null,
 *   seoCanonicalUrl: string|null,
 *   seoNoIndex: bool,
 *   seoNoFollow: bool,
 *   seoHideInSitemap: bool,
 *   excerptTitle: string|null,
 *   excerptMore: string|null,
 *   excerptDescription: string|null,
 *   excerptImageId: int|null,
 *   excerptIconId: int|null,
 *   tagNames: string[],
 *   categoryIds: int[],
 * }
 * @phpstan-type TypoTrashData array{
 *   id: int|null,
 *   description: string,
 *   publicationId: int|null,
 *   created: string,
 *   changed: string,
 * }
 */
class PublicationTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TagRepositoryInterface $tagRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    public static function getResourceKey(): string
    {
        return Publication::RESOURCE_KEY;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: PublicationAdmin::EDIT_FORM_VIEW,
            resultToView: ['id' => 'id'],
        );
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        /** @var Publication $resource */
        Assert::isInstanceOf($resource, Publication::class);

        $data = [
            'dimensionContents' => [],
            'typos' => [],
            'notified' => $resource->isNotified(),
        ];
        $titles = [];

        /** @var PublicationDimensionContent $dimensionContent */
        foreach ($resource->getDimensionContents() as $dimensionContent) {
            if ('draft' === $dimensionContent->getStage()) {
                if (null !== $dimensionContent->getLocale()) {
                    $titles[$dimensionContent->getLocale()] = $dimensionContent->getTitle() ?? '';
                }

                $data['dimensionContents'][] = [
                    'creatorId' => $dimensionContent->getCreator()?->getId(),
                    'created' => $dimensionContent->getCreated()->format('c'),
                    'changerId' => $dimensionContent->getChanger()?->getId(),
                    'changed' => $dimensionContent->getChanged()->format('c'),
                    'locale' => $dimensionContent->getLocale(),
                    'ghostLocale' => $dimensionContent->getGhostLocale(),
                    'availableLocales' => $dimensionContent->getAvailableLocales(),
                    'mainWebspace' => $dimensionContent->getMainWebspace(),
                    'templateKey' => $dimensionContent->getTemplateKey(),
                    'templateData' => $dimensionContent->getTemplateData(),
                    'seoTitle' => $dimensionContent->getSeoTitle(),
                    'seoDescription' => $dimensionContent->getSeoDescription(),
                    'seoKeywords' => $dimensionContent->getSeoKeywords(),
                    'seoCanonicalUrl' => $dimensionContent->getSeoCanonicalUrl(),
                    'seoNoIndex' => $dimensionContent->getSeoNoIndex(),
                    'seoNoFollow' => $dimensionContent->getSeoNoFollow(),
                    'seoHideInSitemap' => $dimensionContent->getSeoHideInSitemap(),
                    'excerptTitle' => $dimensionContent->getExcerptTitle(),
                    'excerptMore' => $dimensionContent->getExcerptMore(),
                    'excerptDescription' => $dimensionContent->getExcerptDescription(),
                    'excerptImageId' => $dimensionContent->getExcerptImage() ? $dimensionContent->getExcerptImage()['id'] : null,
                    'excerptIconId' => $dimensionContent->getExcerptIcon() ? $dimensionContent->getExcerptIcon()['id'] : null,
                    'tagNames' => $dimensionContent->getExcerptTagNames(),
                    'categoryIds' => $dimensionContent->getExcerptCategoryIds(),
                ];
            }
        }

        foreach ($resource->getTypos() as $typo) {
            $data['typos'][] = [
                'id' => $typo->getId(),
                'description' => $typo->getDescription(),
                'publicationId' => $typo->getPublication()->getId(),
                'created' => $typo->getCreated()->format('c'),
                'changed' => $typo->getChanged()->format('c'),
            ];
        }

        return $this->trashItemRepository->create(
            resourceKey: Publication::RESOURCE_KEY,
            resourceId: (string) $resource->getId(),
            resourceTitle: $titles,
            restoreData: $data,
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: PublicationAdmin::SECURITY_CONTEXT,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        /** @var TrashData $data */
        $data = $trashItem->getRestoreData();

        $publication = new Publication();
        $publication
            ->setNotified($data['notified']);

        /** @var DimensionContentTrashData $dimensionContentData */
        foreach ($data['dimensionContents'] as $dimensionContentData) {
            $publication->addDimensionContent(
                $dimensionContent = $publication->createDimensionContent(),
            );

            $dimensionContent->setStage('draft');
            $dimensionContent->setWorkflowPlace(WorkflowInterface::WORKFLOW_PLACE_DRAFT);
            $dimensionContent->setWorkflowPublished(null);

            $dimensionContent
                ->setCreated(new \DateTime($dimensionContentData['created']))
                ->setChanged(new \DateTime($dimensionContentData['changed']));
            if (null !== $dimensionContentData['creatorId']) {
                $dimensionContent->setCreator($this->userRepository->find($dimensionContentData['creatorId']));
            }
            if (null !== $dimensionContentData['changerId']) {
                $dimensionContent->setChanger($this->userRepository->find($dimensionContentData['changerId']));
            }

            $dimensionContent->setLocale($dimensionContentData['locale']);
            $dimensionContent->setGhostLocale($dimensionContentData['ghostLocale']);
            foreach ($dimensionContentData['availableLocales'] ?? [] as $availableLocale) {
                $dimensionContent->addAvailableLocale($availableLocale);
            }

            $dimensionContent->setMainWebspace($dimensionContentData['mainWebspace']);
            $dimensionContent->setTemplateKey($dimensionContentData['templateKey']);
            $dimensionContent->setTemplateData($dimensionContentData['templateData']);

            $dimensionContent->setSeoTitle($dimensionContentData['seoTitle']);
            $dimensionContent->setSeoDescription($dimensionContentData['seoDescription']);
            $dimensionContent->setSeoKeywords($dimensionContentData['seoKeywords']);
            $dimensionContent->setSeoCanonicalUrl($dimensionContentData['seoCanonicalUrl']);
            $dimensionContent->setSeoNoIndex($dimensionContentData['seoNoIndex']);
            $dimensionContent->setSeoNoFollow($dimensionContentData['seoNoFollow']);
            $dimensionContent->setSeoHideInSitemap($dimensionContentData['seoHideInSitemap']);

            $dimensionContent->setExcerptTitle($dimensionContentData['excerptTitle']);
            $dimensionContent->setExcerptMore($dimensionContentData['excerptMore']);
            $dimensionContent->setExcerptDescription($dimensionContentData['excerptDescription']);
            $dimensionContent->setExcerptImage(
                $dimensionContentData['excerptImageId']
                ? ['id' => $dimensionContentData['excerptImageId']]
                : null,
            );
            $dimensionContent->setExcerptIcon(
                $dimensionContentData['excerptIconId']
                ? ['id' => $dimensionContentData['excerptIconId']]
                : null,
            );
            $dimensionContent->setExcerptTags(
                $this->tagRepository->findBy(['name' => $dimensionContentData['tagNames']]),
            );
            $dimensionContent->setExcerptCategories(
                $this->categoryRepository->findCategoriesByIds($dimensionContentData['categoryIds']),
            );
        }

        $this->eventDispatcher->dispatch(new RestoredPublicationEvent($publication));
        $this->doctrineRestoreHelper->persistAndFlushWithId(
            $publication,
            (int) $trashItem->getResourceId(),
        );

        /** @var TypoTrashData $typoData */
        foreach ($data['typos'] as $typoData) {
            $typo = new PublicationTypo(
                publication: $publication,
                description: $typoData['description'],
            );
            $typo
                ->setCreated(new \DateTime($typoData['created']))
                ->setChanged(new \DateTime($typoData['changed']));

            $this->doctrineRestoreHelper->persistAndFlushWithId(
                $typo,
                (int) $typoData['id'],
            );
        }

        return $publication;
    }
}
