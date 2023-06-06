<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Trash;

use App\Domain\Entity\Definition;
use App\Domain\Event\Definition\RestoredDefinitionEvent;
use App\Domain\Repository\DefinitionRepositoryInterface;
use App\Infrastructure\Sulu\Admin\DefinitionAdmin;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
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
 * @phpstan-type TrashData array<string, array{
 *    title: string|null,
 *    description: string|null,
 *    created: string|null,
 *    changed: string|null,
 *    creatorId: int|null,
 *    changerId: int|null,
 *    routePath: string|null
 * }>
 */
class DefinitionTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        private readonly RouteManagerInterface $routeManager,
        private readonly UserRepositoryInterface $userRepository,
        private readonly DefinitionRepositoryInterface $definitionRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function getResourceKey(): string
    {
        return Definition::RESOURCE_KEY;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: DefinitionAdmin::EDIT_FORM_VIEW,
            resultToView: ['id' => 'id'],
        );
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        Assert::isInstanceOf($resource, Definition::class);

        return $this->trashItemRepository->create(
            resourceKey: Definition::RESOURCE_KEY,
            resourceId: (string) $resource->getId(),
            resourceTitle: $this->definitionToTrashTitles($resource),
            restoreData: $this->definitionToTrashData($resource),
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: DefinitionAdmin::SECURITY_CONTEXT,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $definition = $this->trashItemToDefinition($trashItem);
        $this->eventDispatcher->dispatch(new RestoredDefinitionEvent($definition));
        $this->doctrineRestoreHelper->persistAndFlushWithId($definition, (int) $trashItem->getResourceId());

        $this->restoreDefinitionRoutes($trashItem);
        $this->definitionRepository->save($definition);

        return $definition;
    }

    /** @return TrashData */
    private function definitionToTrashData(Definition $definition): array
    {
        /** @var TrashData */
        $data = [];

        foreach ($definition->getLocales() as $locale) {
            $definition->setLocale($locale);

            $data[$locale] = [
                'title' => $definition->getTitle(),
                'description' => $definition->getDescription(),
                'created' => $definition->getCreated()?->format('c'),
                'changed' => $definition->getChanged()?->format('c'),
                'creatorId' => $definition->getCreator()?->getId(),
                'changerId' => $definition->getChanger()?->getId(),
                'routePath' => $definition->getRoutePath(),
            ];
        }

        return $data;
    }

    /** @return array<string, string> */
    private function definitionToTrashTitles(Definition $definition): array
    {
        /** @var array<string, string> */
        $titles = [];

        foreach ($definition->getLocales() as $locale) {
            $definition->setLocale($locale);
            $titles[$locale] = $definition->getTitle() ?? '';
        }

        return $titles;
    }

    private function trashItemToDefinition(TrashItemInterface $trashItem): Definition
    {
        /** @var TrashData */
        $data = $trashItem->getRestoreData();
        $definition = new Definition();

        foreach ($data as $locale => $translationData) {
            $definition
                ->setLocale($locale)
                ->setTitle($translationData['title'] ?? '')
                ->setDescription($translationData['description'] ?? '')
                ->setCreated(new \DateTime($translationData['created'] ?? ''))
                ->setChanged(new \DateTime($translationData['changed'] ?? ''))
                ->setRoutePath($translationData['routePath'] ?? '');

            if (null !== $translationData['creatorId']) {
                $definition->setCreator($this->userRepository->find($translationData['creatorId']));
            }

            if (null !== $translationData['changerId']) {
                $definition->setChanger($this->userRepository->find($translationData['changerId']));
            }
        }

        return $definition;
    }

    public function restoreDefinitionRoutes(TrashItemInterface $trashItem): void
    {
        /** @var TrashData */
        $data = $trashItem->getRestoreData();

        foreach ($data as $locale => $translationData) {
            if (null !== $translationData['routePath']) {
                $this->routeManager->createOrUpdateByAttributes(
                    Definition::class,
                    $trashItem->getResourceId(),
                    $locale,
                    $translationData['routePath'],
                );
            }
        }
    }
}
