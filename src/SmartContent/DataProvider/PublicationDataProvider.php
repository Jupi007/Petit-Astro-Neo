<?php

declare(strict_types=1);

namespace App\SmartContent\DataProvider;

use App\Entity\Publication;
use App\ReferenceStore\PublicationReferenceStore;
use App\Repository\PublicationDataProviderRepository;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\SmartContent\Provider\ContentDataProvider;
use Sulu\Component\Serializer\ArraySerializerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sulu.smart_content.data_provider', [
    'alias' => Publication::RESOURCE_KEY,
])]
class PublicationDataProvider extends ContentDataProvider
{
    public function __construct(
        PublicationDataProviderRepository $repository,
        ArraySerializerInterface $arraySerializer,
        ContentManagerInterface $contentManager,
        PublicationReferenceStore $referenceStore,
    ) {
        parent::__construct(
            $repository,
            $arraySerializer,
            $contentManager,
            $referenceStore,
        );
    }
}
