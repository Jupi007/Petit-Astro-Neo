<?php

declare(strict_types=1);

namespace App\Event\NewsletterRegistration;

use App\Event\Trait\CreatedActivityEventTrait;

class CreatedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use CreatedActivityEventTrait;
}
