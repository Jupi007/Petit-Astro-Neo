<?php

declare(strict_types=1);

namespace App\ContentType;

use App\Entity\Definition;
use App\Repository\DefinitionRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class DefinitionsSelectionContentType extends SimpleContentType
{
    public function __construct(
        private readonly DefinitionRepository $definitionRepository,
    ) {
        parent::__construct('definition_selection', []);
    }

    /**
     * @return Definition[]
     */
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
