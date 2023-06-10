<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\EntityTranslationInterface;
use App\Entity\Contract\PersistableEntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity]
class DefinitionTranslation implements PersistableEntityInterface, EntityTranslationInterface, AuditableInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 191, nullable: true)]
    private ?string $routePath = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Definition::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Definition $definition,
        #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
        private string $locale,
    ) {
    }

    public function getDefinition(): Definition
    {
        return $this->definition;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRoutePath(): ?string
    {
        return $this->routePath;
    }

    public function setRoutePath(?string $routePath): self
    {
        $this->routePath = $routePath;

        return $this;
    }
}
