<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\NewsletterRegistration;

use App\SuluDomainEvent\Trait\RestoredActivityEventTrait;

class RestoredNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RestoredActivityEventTrait;
}
