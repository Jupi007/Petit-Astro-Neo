<?php

declare(strict_types=1);

namespace App\Tests\Application\Entity;

use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\EntityTranslationInterface;
use App\Entity\Contract\PersistableEntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TranslationTestEntity implements PersistableEntityInterface, EntityTranslationInterface
{
    use PersistableEntityTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: LocalizedTestEntity::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private LocalizedTestEntity $entity,
        #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
        private string $locale,
    ) {
    }

    public function getEntity(): LocalizedTestEntity
    {
        return $this->entity;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
