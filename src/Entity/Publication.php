<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityTrait;

/** @implements ContentRichEntityInterface<PublicationDimensionContent> */
#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication implements PersistableEntityInterface, ContentRichEntityInterface
{
    /** @template-use ContentRichEntityTrait<PublicationDimensionContent> */
    use ContentRichEntityTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'publications';
    final public const RESOURCE_ICON = 'su-news';
    final public const TEMPLATE_TYPE = 'publication';

    /** @var ArrayCollection<int, PublicationDimensionContent> */
    #[ORM\OneToMany(
        targetEntity: PublicationDimensionContent::class,
        mappedBy: 'publication',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected $dimensionContents;

    /** @var ArrayCollection<int, PublicationTypo> */
    #[ORM\OneToMany(mappedBy: 'publication', targetEntity: PublicationTypo::class, orphanRemoval: true)]
    private Collection $typos;

    public function __construct()
    {
        $this->typos = new ArrayCollection();
    }

    public function createDimensionContent(): PublicationDimensionContent
    {
        return new PublicationDimensionContent($this);
    }

    /** @return Collection<int, PublicationTypo> */
    public function getTypos(): Collection
    {
        return $this->typos;
    }
}
