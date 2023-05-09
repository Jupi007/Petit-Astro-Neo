<?php

declare(strict_types=1);

namespace App\ActivityEvent\NewsletterRegistration;

use App\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RestoredActivityEventTrait;
}
