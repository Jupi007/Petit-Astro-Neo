<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TrashableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\PublicationTypoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: PublicationTypoRepository::class)]
class PublicationTypo implements PersistableEntityInterface, AuditableInterface, TrashableEntityInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'publication_typos';
    final public const RESOURCE_ICON = 'su-unpublish';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'typos')]
        #[ORM\JoinColumn(nullable: false)]
        private Publication $publication,
    ) {
    }

    public static function getResourceKey(): string
    {
        return self::RESOURCE_KEY;
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

    public function getPublication(): Publication
    {
        return $this->publication;
    }
}
