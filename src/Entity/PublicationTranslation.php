<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TranslationInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\PublicationTranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: PublicationTranslationRepository::class)]
class PublicationTranslation implements PersistableEntityInterface, TranslationInterface, AuditableInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $subtitle = null;

    /** @var mixed[] */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $blocks = [];

    #[ORM\ManyToOne(targetEntity: RouteInterface::class, cascade: ['all'], inversedBy: 'target')]
    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?RouteInterface $route = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Publication::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Publication $publication,
        #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
        private string $locale,
    ) {
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

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /** @return mixed[] */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /** @param mixed[] $blocks */
    public function setBlocks(array $blocks): self
    {
        $this->blocks = $blocks;

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

    public function getPublication(): Publication
    {
        return $this->publication;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
