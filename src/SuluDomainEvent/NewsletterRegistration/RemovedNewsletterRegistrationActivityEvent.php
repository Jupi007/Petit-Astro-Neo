<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\NewsletterRegistration;

use App\SuluDomainEvent\Trait\RemovedActivityEventTrait;

class RemovedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RemovedActivityEventTrait;
}
