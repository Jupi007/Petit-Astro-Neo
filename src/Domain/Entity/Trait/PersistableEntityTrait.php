<?php

declare(strict_types=1);

namespace App\Domain\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait PersistableEntityTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
