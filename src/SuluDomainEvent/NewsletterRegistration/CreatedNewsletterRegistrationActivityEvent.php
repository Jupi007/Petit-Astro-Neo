<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\NewsletterRegistration;

use App\SuluDomainEvent\Trait\CreatedActivityEventTrait;

class CreatedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use CreatedActivityEventTrait;
}
