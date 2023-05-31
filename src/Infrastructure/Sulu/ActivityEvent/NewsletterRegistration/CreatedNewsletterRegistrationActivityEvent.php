<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\NewsletterRegistration;

use App\Infrastructure\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use CreatedActivityEventTrait;
}
