<?php

declare(strict_types=1);

namespace App\ActivityEvent\NewsletterRegistration;

use App\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use CreatedActivityEventTrait;
}
