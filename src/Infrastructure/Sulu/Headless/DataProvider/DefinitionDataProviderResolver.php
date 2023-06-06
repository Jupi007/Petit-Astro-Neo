<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Headless\DataProvider;

use App\Entity\Definition;
use App\Infrastructure\Sulu\SmartContent\DataProvider\DefinitionDataProvider;
use App\UserInterface\API\Representation\DefinitionRepresentation;
use JMS\Serializer\ArrayTransformerInterface;
use Sulu\Bundle\HeadlessBundle\Content\DataProviderResolver\DataProviderResolverInterface;
use Sulu\Bundle\HeadlessBundle\Content\DataProviderResolver\DataProviderResult;
use Sulu\Component\SmartContent\Configuration\ProviderConfigurationInterface;

class DefinitionDataProviderResolver implements DataProviderResolverInterface
{
    public function __construct(
        private readonly DefinitionDataProvider $dataProvider,
        private readonly ArrayTransformerInterface $serializer,
    ) {
    }

    public static function getDataProvider(): string
    {
        return Definition::RESOURCE_KEY;
    }

    public function getProviderConfiguration(): ProviderConfigurationInterface
    {
        return $this->dataProvider->getConfiguration();
    }

    public function getProviderDefaultParams(): array
    {
        return $this->dataProvider->getDefaultPropertyParameter();
    }

    public function resolve(array $filters, array $propertyParameters, array $options = [], int $limit = null, int $page = 1, int $pageSize = null): DataProviderResult
    {
        $providerResult = $this->dataProvider->resolveResourceItems(
            $filters,
            $propertyParameters,
            $options,
            $limit,
            $page,
            $pageSize,
        );

        $definitionRepresentations = [];
        foreach ($providerResult->getItems() as $resultItem) {
            $definitionRepresentations[] = new DefinitionRepresentation($resultItem->getResource());
        }

        return new DataProviderResult($this->serializer->toArray($definitionRepresentations), $providerResult->getHasNextPage());
    }
}
