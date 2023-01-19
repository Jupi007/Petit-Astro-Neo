<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\DefinitionAdmin;
use App\Entity\Definition;
use App\Repository\DefinitionRepository;
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
 *    content: string|null,
 *    created: string|null,
 *    creatorId: int|null,
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
        private readonly DefinitionRepository $definitionRepository,
    ) {
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        /** @var Definition */
        $definition = $resource;

        return $this->trashItemRepository->create(
            resourceKey: Definition::RESOURCE_KEY,
            resourceId: (string) $definition->getId(),
            resourceTitle: $this->definitionToTrashTitles($definition),
            restoreData: $this->definitionToTrashData($definition),
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
        $this->doctrineRestoreHelper->persistAndFlushWithId($definition, (int) $trashItem->getResourceId());

        $this->restoreDefinitionRoutes($definition, $trashItem);
        $this->definitionRepository->save($definition);

        return $definition;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(
            view: DefinitionAdmin::EDIT_FORM_VIEW,
            resultToView: ['id' => 'id'],
        );
    }

    public static function getResourceKey(): string
    {
        return Definition::RESOURCE_KEY;
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
                'content' => $definition->getContent(),
                'created' => $definition->getCreated()?->format('c'),
                'creatorId' => $definition->getCreator()?->getId(),
                'routePath' => $definition->getRoute()?->getPath(),
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
                ->setContent($translationData['content'] ?? '')
                ->setCreated(new \DateTime($translationData['created'] ?? ''));

            if (null !== $translationData['creatorId']) {
                $definition->setCreator($this->userRepository->find($translationData['creatorId']));
            }
        }

        return $definition;
    }

    public function restoreDefinitionRoutes(Definition $definition, TrashItemInterface $trashItem): void
    {
        /** @var TrashData */
        $data = $trashItem->getRestoreData();

        foreach ($data as $locale => $translationData) {
            if (null !== $translationData['routePath']) {
                $definition->setLocale($locale);
                $this->routeManager->create($definition, $translationData['routePath']);
            }
        }
    }
}
