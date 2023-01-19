<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface PersistableEntityInterface
{
    public function getId(): ?int;
}
