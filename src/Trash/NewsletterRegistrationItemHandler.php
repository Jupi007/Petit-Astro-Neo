<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\NewsletterRegistrationAdmin;
use App\Entity\NewsletterRegistration;
use App\Event\NewsletterRegistration\RestoredNewsletterRegistrationActivityEvent;
use App\Exception\NewsletterRegistrationEmailNotUniqueException;
use App\Repository\NewsletterRegistrationRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;

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
class NewsletterRegistrationItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly NewsletterRegistrationRepository $registrationRepository,
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly DomainEventCollectorInterface $domainEventCollector,
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
        /** @var NewsletterRegistration */
        $registration = $resource;

        $data = [
            'email' => $registration->getEmail(),
            'locale' => $registration->getLocale(),
            'created' => $registration->getCreated()->format('c'),
            'changed' => $registration->getChanged()->format('c'),
            'creatorId' => $registration->getCreator()?->getId(),
            'changerId' => $registration->getChanger()?->getId(),
        ];

        return $this->trashItemRepository->create(
            resourceKey: NewsletterRegistration::RESOURCE_KEY,
            resourceId: (string) $registration->getId(),
            resourceTitle: $registration->getEmail(),
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

        if (null !== $this->registrationRepository->findOneBy(['email' => $data['email']])) {
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

        $this->domainEventCollector->collect(new RestoredNewsletterRegistrationActivityEvent($registration));
        $this->doctrineRestoreHelper->persistAndFlushWithId($registration, (int) $trashItem->getResourceId());

        return $registration;
    }
}
