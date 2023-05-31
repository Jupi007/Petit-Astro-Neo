<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\NewsletterRegistration;

use App\Infrastructure\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RestoredActivityEventTrait;
}
