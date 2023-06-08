<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface TrashableEntityInterface
{
    public static function getResourceKey(): string;
}
