<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TrashableEntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity]
class PublicationTypo implements PersistableEntityInterface, AuditableInterface, TrashableEntityInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'publication_typos';
    final public const RESOURCE_ICON = 'su-unpublish';

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'typos')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly Publication $publication,
        #[ORM\Column(type: Types::TEXT)]
        private string $description,
    ) {
        $publication->getTypos()->add($this);
    }

    public static function getResourceKey(): string
    {
        return self::RESOURCE_KEY;
    }

    public function getDescription(): string
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
