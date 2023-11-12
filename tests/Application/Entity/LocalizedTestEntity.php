<?php

declare(strict_types=1);

namespace App\Tests\Application\Entity;

use App\Entity\Common\LocalizableEntityTrait;
use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\LocalizableEntityInterface;
use App\Entity\Contract\PersistableEntityInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class LocalizedTestEntity implements PersistableEntityInterface, LocalizableEntityInterface
{
    /** @use LocalizableEntityTrait<TranslationTestEntity> */
    use LocalizableEntityTrait;
    use PersistableEntityTrait;

    /** @var Collection<string, TranslationTestEntity> */
    #[ORM\OneToMany(
        targetEntity: TranslationTestEntity::class,
        mappedBy: 'entity',
        cascade: ['persist'],
        indexBy: 'locale',
        orphanRemoval: true,
    )]
    private Collection $translations;

    public function __construct(
        #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
        private bool $published = true,
    ) {
        $this->__localizableEntityTraitConstructor();
    }

    private function createTranslation(): TranslationTestEntity
    {
        return new TranslationTestEntity($this, $this->locale);
    }

    public function getPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->getTranslation()->getTitle();
    }

    public function setTitle(string $title): self
    {
        $this->getTranslation()->setTitle($title);

        return $this;
    }
}
