<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Trash;

use App\Domain\Entity\Publication;
use App\Domain\Entity\PublicationTypo;
use App\Domain\Event\PublicationTypo\RestoredPublicationTypoEvent;
use App\Domain\Exception\PublicationNotFoundException;
use App\Domain\Repository\PublicationRepositoryInterface;
use App\Infrastructure\Sulu\Admin\PublicationTypoAdmin;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-type TrashData array{
 *    description: string,
 *    publicationId: int|null,
 *    created: string|null,
 *    changed: string|null,
 * }
 */
class PublicationTypoTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly PublicationRepositoryInterface $publicationRepository,
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function getResourceKey(): string
    {
        return PublicationTypo::RESOURCE_KEY;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: PublicationTypoAdmin::LIST_VIEW_PUBLICATION,
            resultToView: ['id' => 'publication.id'],
        );
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        Assert::isInstanceOf($resource, PublicationTypo::class);

        /** @var TrashData $data */
        $data = [
            'description' => $resource->getDescription(),
            'publicationId' => $resource->getPublication()->getId(),
            'created' => $resource->getCreated()->format('c'),
            'changed' => $resource->getChanged()->format('c'),
        ];

        return $this->trashItemRepository->create(
            resourceKey: PublicationTypo::RESOURCE_KEY,
            resourceId: (string) $resource->getId(),
            resourceTitle: $resource->getDescription(),
            restoreData: $data,
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: PublicationTypoAdmin::SECURITY_CONTEXT,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        /** @var TrashData $data */
        $data = $trashItem->getRestoreData();

        $publication = $this->publicationRepository->findOne($data['publicationId']);
        if (!$publication instanceof Publication) {
            throw new PublicationNotFoundException();
        }

        $typo = new PublicationTypo(
            publication: $publication,
            description: $data['description'],
        );
        $typo
            ->setCreated(new \DateTime($data['created'] ?? ''))
            ->setChanged(new \DateTime($data['changed'] ?? ''));

        $this->eventDispatcher->dispatch(new RestoredPublicationTypoEvent($typo));
        $this->doctrineRestoreHelper->persistAndFlushWithId($typo, (int) $trashItem->getResourceId());

        return $typo;
    }
}
