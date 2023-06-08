<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\NewsletterRegistration;

use App\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RestoredActivityEventTrait;
}
