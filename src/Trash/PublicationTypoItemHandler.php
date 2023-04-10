<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\PublicationTypoAdmin;
use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\RestoredPublicationTypoActivityEvent;
use App\Exception\PublicationNotFoundException;
use App\Repository\PublicationRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-type TrashData array{
 *    description: string|null,
 *    publicationId: int|null,
 *    created: string|null,
 *    changed: string|null,
 * }
 */
class PublicationTypoItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly DomainEventCollectorInterface $domainEventCollector,
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

        $data = [
            'description' => $resource->getDescription(),
            'publicationId' => $resource->getPublication()->getId(),
            'created' => $resource->getCreated()->format('c'),
            'changed' => $resource->getChanged()->format('c'),
        ];

        return $this->trashItemRepository->create(
            resourceKey: PublicationTypo::RESOURCE_KEY,
            resourceId: (string) $resource->getId(),
            resourceTitle: $resource->getDescription() ?? '',
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

        $publication = $this->publicationRepository->find($data['publicationId']);
        if (null === $publication) {
            throw new PublicationNotFoundException();
        }

        $typo = new PublicationTypo($publication);
        $typo
            ->setDescription($data['description'] ?? '')
            ->setCreated(new \DateTime($data['created'] ?? ''))
            ->setChanged(new \DateTime($data['changed'] ?? ''));

        $this->domainEventCollector->collect(new RestoredPublicationTypoActivityEvent($typo));
        $this->doctrineRestoreHelper->persistAndFlushWithId($typo, (int) $trashItem->getResourceId());

        return $typo;
    }
}
