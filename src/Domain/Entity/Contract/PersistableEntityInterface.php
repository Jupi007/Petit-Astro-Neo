<?php

declare(strict_types=1);

namespace App\Domain\Entity\Contract;

interface PersistableEntityInterface
{
    public function getId(): ?int;
}
