<?php

declare(strict_types=1);

namespace App\Domain\Event\Publication;

use App\Domain\Entity\Publication;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractPublicationEvent extends Event
{
    public function __construct(
        private readonly Publication $resource,
    ) {
    }

    public function getResource(): Publication
    {
        return $this->resource;
    }
}
