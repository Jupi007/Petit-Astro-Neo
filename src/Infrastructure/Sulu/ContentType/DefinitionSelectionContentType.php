<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ContentType;

use App\Entity\Definition;
use App\Repository\DefinitionRepositoryInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sulu.content.type', [
    'alias' => Definition::RESOURCE_KEY . '_selection',
])]
class DefinitionSelectionContentType extends SimpleContentType
{
    public function __construct(
        private readonly DefinitionRepositoryInterface $definitionRepository,
    ) {
        parent::__construct(
            Definition::RESOURCE_KEY . '_selection',
        );
    }

    /** @return Definition[] */
    public function getContentData(PropertyInterface $property): array
    {
        $definitions = $this->definitionRepository->findBy([
            'id' => $property->getValue(),
        ]);

        foreach ($definitions as $definition) {
            $definition->setLocale($property->getStructure()->getLanguageCode());
        }

        return $definitions;
    }

    public function getViewData(PropertyInterface $property)
    {
        return [
            'ids' => $property->getValue(),
        ];
    }
}
