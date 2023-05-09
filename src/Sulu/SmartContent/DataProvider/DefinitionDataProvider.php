<?php

declare(strict_types=1);

namespace App\Sulu\SmartContent\DataProvider;

use App\Entity\Definition;
use App\Sulu\Admin\DefinitionAdmin;
use App\Sulu\SmartContent\DataItem\DefinitionDataItem;
use App\Sulu\SmartContent\Repository\DefinitionDataProviderRepository;
use Sulu\Component\Serializer\ArraySerializerInterface;
use Sulu\Component\SmartContent\Orm\BaseDataProvider;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sulu.smart_content.data_provider', [
    'alias' => Definition::RESOURCE_KEY,
])]
class DefinitionDataProvider extends BaseDataProvider
{
    public function __construct(
        DefinitionDataProviderRepository $repository,
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
