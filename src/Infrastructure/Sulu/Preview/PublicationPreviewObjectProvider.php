<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Preview;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Infrastructure\Sulu\Admin\PublicationAdmin;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentDataMapper\ContentDataMapperInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\ContentResolverInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Preview\ContentObjectProvider;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/** @extends ContentObjectProvider<PublicationDimensionContent, Publication> */
#[AutoconfigureTag('sulu_preview.object_provider', [
    'provider-key' => Publication::RESOURCE_KEY,
])]
class PublicationPreviewObjectProvider extends ContentObjectProvider
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ContentResolverInterface $contentResolver,
        ContentDataMapperInterface $contentDataMapper,
    ) {
        parent::__construct(
            $entityManager,
            $contentResolver,
            $contentDataMapper,
            Publication::class,
            PublicationAdmin::SECURITY_CONTEXT,
        );
    }
}
