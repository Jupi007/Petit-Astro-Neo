<?php

declare(strict_types=1);

namespace App\Sulu\Headless\DataProvider;

use App\Sulu\SmartContent\DataProvider\PublicationDataProvider;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Structure\ContentStructureBridgeFactory;
use Sulu\Bundle\HeadlessBundle\Content\DataProviderResolver\DataProviderResolverInterface;
use Sulu\Bundle\HeadlessBundle\Content\DataProviderResolver\DataProviderResult;
use Sulu\Bundle\HeadlessBundle\Content\StructureResolverInterface;
use Sulu\Component\Content\Compat\PropertyParameter;
use Sulu\Component\SmartContent\Configuration\ProviderConfigurationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class AbstractContentDataProviderResolver implements DataProviderResolverInterface
{
    public function __construct(
        private readonly PublicationDataProvider $dataProvider,
        private readonly StructureResolverInterface $structureResolver,
        #[Autowire('@sulu_content.content_structure_bridge_factory')]
        private readonly ContentStructureBridgeFactory $contentStructureBridgeFactory,
    ) {
    }

    abstract public static function getDataProvider(): string;

    public function getProviderConfiguration(): ProviderConfigurationInterface
    {
        return $this->dataProvider->getConfiguration();
    }

    public function getProviderDefaultParams(): array
    {
        return $this->dataProvider->getDefaultPropertyParameter();
    }

    public function resolve(array $filters, array $propertyParameters, array $options = [], ?int $limit = null, int $page = 1, ?int $pageSize = null): DataProviderResult
    {
        $providerResult = $this->dataProvider->resolveResourceItems(
            $filters,
            $propertyParameters,
            $options,
            $limit,
            $page,
            $pageSize,
        );

        /** @var string $locale */
        $locale = $options['locale'];

        $contentStructures = [];
        foreach ($providerResult->getItems() as $resultItem) {
            $contentStructures[] = $this->contentStructureBridgeFactory->getBridge($resultItem->getResource(), $resultItem->getId(), $locale);
        }

        /** @var PropertyParameter[] $propertiesParamValue */
        $propertiesParamValue = isset($propertyParameters['properties']) ? $propertyParameters['properties']->getValue() : [];

        $propertyMap = [
            'title' => 'title',
            'url' => 'url',
        ];

        foreach ($propertiesParamValue as $propertiesParamEntry) {
            $paramName = $propertiesParamEntry->getName();
            $paramValue = $propertiesParamEntry->getValue();
            $propertyMap[$paramName] = \is_string($paramValue) ? $paramValue : $paramName;
        }

        $resolvedArticles = [];
        foreach ($contentStructures as $contentStructure) {
            $resolvedArticles[] = $this->structureResolver->resolveProperties($contentStructure, $propertyMap, $locale);
        }

        return new DataProviderResult(\array_values($resolvedArticles), $providerResult->getHasNextPage());
    }
}
