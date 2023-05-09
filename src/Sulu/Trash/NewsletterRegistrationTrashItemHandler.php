<?php

declare(strict_types=1);

namespace App\Sulu\Trash;

use App\DomainEvent\NewsletterRegistration\RestoredNewsletterRegistrationEvent;
use App\Entity\NewsletterRegistration;
use App\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Repository\NewsletterRegistrationRepository;
use App\Sulu\Admin\NewsletterRegistrationAdmin;
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
 *    email: string,
 *    locale: string,
 *    created: string|null,
 *    changed: string|null,
 *    creatorId: int|null,
 *    changerId: int|null,
 * }
 */
class NewsletterRegistrationTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly NewsletterRegistrationRepository $registrationRepository,
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function getResourceKey(): string
    {
        return NewsletterRegistration::RESOURCE_KEY;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: NewsletterRegistrationAdmin::EDIT_FORM_VIEW,
            resultToView: ['id' => 'id'],
        );
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        Assert::isInstanceOf($resource, NewsletterRegistration::class);

        $data = [
            'email' => $resource->getEmail(),
            'locale' => $resource->getLocale(),
            'created' => $resource->getCreated()->format('c'),
            'changed' => $resource->getChanged()->format('c'),
            'creatorId' => $resource->getCreator()?->getId(),
            'changerId' => $resource->getChanger()?->getId(),
        ];

        return $this->trashItemRepository->create(
            resourceKey: NewsletterRegistration::RESOURCE_KEY,
            resourceId: (string) $resource->getId(),
            resourceTitle: $resource->getEmail(),
            restoreData: $data,
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: NewsletterRegistrationAdmin::SECURITY_CONTEXT,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        /** @var TrashData $data */
        $data = $trashItem->getRestoreData();

        if ($this->registrationRepository->findOneBy(['email' => $data['email']]) instanceof NewsletterRegistration) {
            throw new NewsletterRegistrationEmailNotUniqueException($data['email']);
        }

        $registration = new NewsletterRegistration(
            email: $data['email'],
            locale: $data['locale'],
        );
        $registration
            ->setCreated(new \DateTime($data['created'] ?? ''))
            ->setChanged(new \DateTime($data['changed'] ?? ''));

        if (null !== $data['creatorId']) {
            $registration->setCreator($this->userRepository->find($data['creatorId']));
        }

        if (null !== $data['changerId']) {
            $registration->setChanger($this->userRepository->find($data['changerId']));
        }

        $this->eventDispatcher->dispatch(new RestoredNewsletterRegistrationEvent($registration));
        $this->doctrineRestoreHelper->persistAndFlushWithId($registration, (int) $trashItem->getResourceId());

        return $registration;
    }
}
