<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\NewsletterRegistration;

use App\SuluDomainEvent\Trait\ModifiedActivityEventTrait;

class ModifiedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use ModifiedActivityEventTrait;
}
