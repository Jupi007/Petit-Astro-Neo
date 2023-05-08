<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TrashableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityTrait;

/** @implements ContentRichEntityInterface<PublicationDimensionContent> */
#[ORM\Entity]
class Publication implements PersistableEntityInterface, ContentRichEntityInterface, TrashableEntityInterface
{
    /** @template-use ContentRichEntityTrait<PublicationDimensionContent> */
    use ContentRichEntityTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'publications';
    final public const RESOURCE_ICON = 'su-news';
    final public const TEMPLATE_TYPE = 'publication';

    /** @var Collection<int, PublicationDimensionContent> */
    #[ORM\OneToMany(
        targetEntity: PublicationDimensionContent::class,
        mappedBy: 'publication',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected $dimensionContents;

    /** @var ArrayCollection<int, PublicationTypo> */
    #[ORM\OneToMany(
        targetEntity: PublicationTypo::class,
        mappedBy: 'publication',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    private Collection $typos;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $notified = false;

    public function __construct()
    {
        $this->dimensionContents = new ArrayCollection();
        $this->typos = new ArrayCollection();
    }

    public static function getResourceKey(): string
    {
        return self::RESOURCE_KEY;
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

    public function isNotified(): bool
    {
        return $this->notified;
    }

    public function setNotified(bool $notified): self
    {
        $this->notified = $notified;

        return $this;
    }
}
