<?php

declare(strict_types=1);

namespace App\Domain\Event\Definition;

use App\Domain\Entity\Definition;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractDefinitionEvent extends Event
{
    public function __construct(
        private readonly Definition $resource,
    ) {
    }

    public function getResource(): Definition
    {
        return $this->resource;
    }
}
