<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TranslationInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\DefinitionTranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: DefinitionTranslationRepository::class)]
class DefinitionTranslation implements PersistableEntityInterface, TranslationInterface, AuditableInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: RouteInterface::class, cascade: ['all'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?RouteInterface $route = null;

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

    public function getRoute(): ?RouteInterface
    {
        return $this->route;
    }

    public function setRoute(RouteInterface $route): self
    {
        $this->route = $route;

        return $this;
    }
}
