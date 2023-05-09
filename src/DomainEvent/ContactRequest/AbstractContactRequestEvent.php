<?php

declare(strict_types=1);

namespace App\DomainEvent\ContactRequest;

use App\Entity\ContactRequest;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractContactRequestEvent extends Event
{
    public function __construct(
        private readonly ContactRequest $resource,
    ) {
    }

    public function getResource(): ContactRequest
    {
        return $this->resource;
    }
}
