<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Headless\ContentType;

use App\Domain\Entity\Definition;
use App\Infrastructure\Sulu\ContentType\DefinitionSelectionContentType;
use App\UserInterface\API\Representation\DefinitionRepresentation;
use JMS\Serializer\ArrayTransformerInterface;
use Sulu\Bundle\HeadlessBundle\Content\ContentTypeResolver\ContentTypeResolverInterface;
use Sulu\Bundle\HeadlessBundle\Content\ContentView;
use Sulu\Component\Content\Compat\PropertyInterface;

class DefinitionSelectionContentTypeResolver implements ContentTypeResolverInterface
{
    public function __construct(
        private readonly DefinitionSelectionContentType $definitionSelectionContentType,
        private readonly ArrayTransformerInterface $serializer,
    ) {
    }

    public static function getContentType(): string
    {
        return Definition::RESOURCE_KEY . '_selection';
    }

    public function resolve($data, PropertyInterface $property, string $locale, array $attributes = []): ContentView
    {
        $definitions = $this->definitionSelectionContentType->getContentData($property);
        \array_walk($definitions, fn (Definition & $definition) => $definition = new DefinitionRepresentation($definition));

        return new ContentView(
            $this->serializer->toArray($definitions),
            $this->definitionSelectionContentType->getViewData($property),
        );
    }
}
