<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\ContactRequestAdmin;
use App\Entity\ContactRequest;
use App\SuluDomainEvent\ContactRequest\RestoredContactRequestActivityEvent;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-type TrashData array{
 *    object: string,
 *    email: string,
 *    message: string,
 *    created: string|null,
 *    changed: string|null,
 *    creatorId: int|null,
 *    changerId: int|null,
 * }
 */
class ContactRequestTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly DomainEventCollectorInterface $domainEventCollector,
    ) {
    }

    public static function getResourceKey(): string
    {
        return ContactRequest::RESOURCE_KEY;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: ContactRequestAdmin::LIST_VIEW,
        );
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        /** @var ContactRequest $resource */
        Assert::isInstanceOf($resource, ContactRequest::class);

        /** @var TrashData $data */
        $data = [
            'object' => $resource->getObject(),
            'email' => $resource->getEmail(),
            'message' => $resource->getMessage(),
            'created' => $resource->getCreated()->format('c'),
            'changed' => $resource->getChanged()->format('c'),
            'creatorId' => $resource->getCreator()?->getId(),
            'changerId' => $resource->getChanger()?->getId(),
        ];

        return $this->trashItemRepository->create(
            resourceKey: ContactRequest::RESOURCE_KEY,
            resourceId: (string) $resource->getId(),
            resourceTitle: $resource->getObject(),
            restoreData: $data,
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: ContactRequestAdmin::SECURITY_CONTEXT,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        /** @var TrashData $data */
        $data = $trashItem->getRestoreData();

        $request = new ContactRequest(
            object: $data['object'],
            email: $data['email'],
            message: $data['message'],
        );
        $request
            ->setCreated(new \DateTime($data['created'] ?? ''))
            ->setChanged(new \DateTime($data['changed'] ?? ''));

        if (null !== $data['creatorId']) {
            $request->setCreator($this->userRepository->find($data['creatorId']));
        }

        if (null !== $data['changerId']) {
            $request->setChanger($this->userRepository->find($data['changerId']));
        }

        $this->domainEventCollector->collect(new RestoredContactRequestActivityEvent($request));
        $this->doctrineRestoreHelper->persistAndFlushWithId($request, (int) $trashItem->getResourceId());

        return $request;
    }
}
