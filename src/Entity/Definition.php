<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Common\LocalizableAuditableTrait;
use App\Entity\Common\LocalizableEntityTrait;
use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\LocalizableEntityInterface;
use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TrashableEntityInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Definition implements PersistableEntityInterface, LocalizableEntityInterface, TrashableEntityInterface
{
    /** @use LocalizableAuditableTrait<DefinitionTranslation> */
    use LocalizableAuditableTrait;
    /** @use LocalizableEntityTrait<DefinitionTranslation> */
    use LocalizableEntityTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'definitions';
    final public const RESOURCE_ICON = 'fa-book';

    /** @var Collection<string, DefinitionTranslation> */
    #[ORM\OneToMany(
        targetEntity: DefinitionTranslation::class,
        mappedBy: 'definition',
        cascade: ['persist'],
        indexBy: 'locale',
        orphanRemoval: true,
    )]
    private Collection $translations;

    public function __construct()
    {
        $this->__localizableEntityTraitConstructor();
    }

    public static function getResourceKey(): string
    {
        return self::RESOURCE_KEY;
    }

    private function createTranslation(): DefinitionTranslation
    {
        return new DefinitionTranslation($this, $this->locale);
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

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(string $description): self
    {
        $this->getTranslation()->setDescription($description);

        return $this;
    }

    public function getRoutePath(): ?string
    {
        return $this->getTranslation()->getRoutePath();
    }

    public function setRoutePath(string $title): self
    {
        $this->getTranslation()->setRoutePath($title);

        return $this;
    }
}
