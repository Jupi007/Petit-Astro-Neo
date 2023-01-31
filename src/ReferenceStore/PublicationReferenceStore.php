<?php

declare(strict_types=1);

namespace App\ReferenceStore;

use App\Entity\Publication;
use Sulu\Bundle\WebsiteBundle\ReferenceStore\ReferenceStore;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sulu_website.reference_store', [
    'alias' => Publication::RESOURCE_KEY,
])]
class PublicationReferenceStore extends ReferenceStore
{
}
