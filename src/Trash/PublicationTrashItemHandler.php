<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\PublicationAdmin;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;

/**
 * @phpstan-type TrashData array<string, array{
 *    title: string|null,
 *    subtitle: string|null,
 *    content: string|null,
 *    created: string|null,
 *    creatorId: int|null,
 *    routePath: string|null
 * }>
 */
class PublicationTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly RouteManagerInterface $routeManager,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PublicationRepository $publicationRepository,
    ) {
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        /** @var Publication */
        $publication = $resource;

        return $this->trashItemRepository->create(
            resourceKey: Publication::RESOURCE_KEY,
            resourceId: (string) $publication->getId(),
            resourceTitle: $this->publicationToTrashTitles($publication),
            restoreData: $this->publicationToTrashData($publication),
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: PublicationAdmin::SECURITY_CONTEXT,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $publication = $this->trashItemToPublication($trashItem);
        $this->doctrineRestoreHelper->persistAndFlushWithId($publication, (int) $trashItem->getResourceId());

        $this->restorePublicationRoutes($publication, $trashItem);
        $this->publicationRepository->save($publication);

        return $publication;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: PublicationAdmin::EDIT_FORM_VIEW,
            resultToView: ['id' => 'id'],
        );
    }

    public static function getResourceKey(): string
    {
        return Publication::RESOURCE_KEY;
    }

    /** @return TrashData */
    private function publicationToTrashData(Publication $publication): array
    {
        /** @var TrashData */
        $data = [];

        foreach ($publication->getLocales() as $locale) {
            $publication->setLocale($locale);

            $data[$locale] = [
                'title' => $publication->getTitle(),
                'subtitle' => $publication->getSubtitle(),
                'content' => (string) \json_encode($publication->getContent()),
                'created' => $publication->getCreated()?->format('c'),
                'creatorId' => $publication->getCreator()?->getId(),
                'routePath' => $publication->getRoute()?->getPath(),
            ];
        }

        return $data;
    }

    /** @return array<string, string> */
    private function publicationToTrashTitles(Publication $publication): array
    {
        /** @var array<string, string> */
        $titles = [];

        foreach ($publication->getLocales() as $locale) {
            $publication->setLocale($locale);
            $titles[$locale] = $publication->getTitle() ?? '';
        }

        return $titles;
    }

    private function trashItemToPublication(TrashItemInterface $trashItem): Publication
    {
        /** @var TrashData */
        $data = $trashItem->getRestoreData();
        $publication = new Publication();

        foreach ($data as $locale => $translationData) {
            $publication
                ->setLocale($locale)
                ->setTitle($translationData['title'] ?? '')
                ->setSubtitle($translationData['subtitle'] ?? '')
                ->setContent((array) \json_decode($translationData['content'] ?? ''))
                ->setCreated(new \DateTime($translationData['created'] ?? ''));

            if (null !== $translationData['creatorId']) {
                $publication->setCreator($this->userRepository->find($translationData['creatorId']));
            }
        }

        return $publication;
    }

    public function restorePublicationRoutes(Publication $publication, TrashItemInterface $trashItem): void
    {
        /** @var TrashData */
        $data = $trashItem->getRestoreData();

        foreach ($data as $locale => $translationData) {
            if (null !== $translationData['routePath']) {
                $publication->setLocale($locale);
                $this->routeManager->create($publication, $translationData['routePath']);
            }
        }
    }
}
