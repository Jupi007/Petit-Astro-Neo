<?php

declare(strict_types=1);

namespace App\SmartContent\DataProvider;

use App\Admin\DefinitionAdmin;
use App\Entity\Definition;
use App\Repository\DefinitionRepository;
use App\SmartContent\DataItem\DefinitionDataItem;
use Sulu\Component\Serializer\ArraySerializerInterface;
use Sulu\Component\SmartContent\Orm\BaseDataProvider;

class DefinitionDataProvider extends BaseDataProvider
{
    public function __construct(
        DefinitionRepository $repository,
        ArraySerializerInterface $serializer,
    ) {
        parent::__construct($repository, $serializer);
    }

    public function getConfiguration()
    {
        if (null === $this->configuration) {
            $this->configuration = self::createConfigurationBuilder()
                ->enableLimit()
                ->enablePagination()
                ->enablePresentAs()
                ->enableSorting([
                    ['column' => null, 'title' => 'sulu_admin.default'],
                    ['column' => 'translation.title', 'title' => 'sulu_admin.title'],
                    ['column' => 'translation.created', 'title' => 'sulu_admin.created'],
                    ['column' => 'translation.changed', 'title' => 'sulu_admin.changed'],
                    ['column' => 'translation.authored', 'title' => 'sulu_admin.authored'],
                ])
                ->enableView(DefinitionAdmin::EDIT_FORM_VIEW, [
                    'id' => 'id',
                ])
                ->getConfiguration();
        }

        return $this->configuration;
    }

    /**
     * @param Definition[] $data
     *
     * @return DefinitionDataItem[]
     */
    protected function decorateDataItems(array $data): array
    {
        return \array_map(
            fn (Definition $definition) => new DefinitionDataItem($definition),
            $data,
        );
    }
}
