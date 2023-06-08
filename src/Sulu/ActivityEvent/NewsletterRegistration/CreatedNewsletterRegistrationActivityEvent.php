<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\NewsletterRegistration;

use App\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use CreatedActivityEventTrait;
}
