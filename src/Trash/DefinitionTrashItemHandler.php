<?php

declare(strict_types=1);

namespace App\Trash;

use App\Admin\DefinitionAdmin;
use App\Entity\Definition;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Sulu\Component\Security\Authentication\UserInterface;

class DefinitionTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    public function __construct(
        private readonly TrashItemRepositoryInterface $trashItemRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly DoctrineRestoreHelperInterface $doctrineRestoreHelper,
    ) {
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        /** @var Definition */
        $definition = $resource;

        return $this->trashItemRepository->create(
            resourceKey: Definition::RESOURCE_KEY,
            resourceId: (string) $definition->getId(),
            resourceTitle: $definition->getTitle() ?? '',
            restoreData: $this->definitionToTrashData($definition),
            restoreType: null,
            restoreOptions: $options,
            resourceSecurityContext: null,
            resourceSecurityObjectType: null,
            resourceSecurityObjectId: null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $definition = $this->trashItemToDefinition($trashItem);
        $this->doctrineRestoreHelper->persistAndFlushWithId($definition, (int) $trashItem->getResourceId());

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

    /**
     * @return array<string, array<string, int|string|null>>
     */
    private function definitionToTrashData(Definition $definition): array
    {
        $data = [];

        foreach ($definition->getTranslations() as $translation) {
            $creator = $translation->getCreator();

            $data[$translation->getLocale()] = [
                'title' => $translation->getTitle(),
                'content' => $translation->getContent(),
                'created' => $translation->getCreated()->format('c'),
                'creatorId' => null !== $creator ? $creator->getId() : null,
            ];
        }

        return $data;
    }

    private function trashItemToDefinition(TrashItemInterface $trashItem): Definition
    {
        /**
         * @var array<string, array{
         *  title: string,
         *  content: string,
         *  created: string,
         *  creatorId: int
         * }>
         */
        $data = $trashItem->getRestoreData();

        $definition = new Definition();

        foreach ($data as $locale => $translationData) {
            $definition->setLocale($locale);
            $definition
                ->setTitle($translationData['title'])
                ->setContent($translationData['content'])
                 ->setCreated(new \DateTime($translationData['created']));

            if (0 !== $translationData['creatorId']) {
                $definition->setCreator($this->entityManager->find(UserInterface::class, $translationData['creatorId']));
            }
        }

        return $definition;
    }
}
