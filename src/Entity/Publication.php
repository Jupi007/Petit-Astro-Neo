<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityTrait;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication implements PersistableEntityInterface, ContentRichEntityInterface
{
    use ContentRichEntityTrait;
    use PersistableEntityTrait;

    public const RESOURCE_KEY = 'publications';
    public const TEMPLATE_TYPE = 'publication';

    /** @var ArrayCollection<int, PublicationDimensionContent> */
    #[ORM\OneToMany(
        targetEntity: PublicationDimensionContent::class,
        mappedBy: 'publication',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected $dimensionContents;

    public function createDimensionContent(): PublicationDimensionContent
    {
        return new PublicationDimensionContent($this);
    }
}
