<?php

declare(strict_types=1);

namespace App\DomainEvent\NewsletterRegistration;

use App\Entity\NewsletterRegistration;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractNewsletterRegistrationEvent extends Event
{
    public function __construct(
        private readonly NewsletterRegistration $resource,
    ) {
    }

    public function getResource(): NewsletterRegistration
    {
        return $this->resource;
    }
}
