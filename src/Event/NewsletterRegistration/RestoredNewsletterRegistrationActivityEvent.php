<?php

declare(strict_types=1);

namespace App\Event\NewsletterRegistration;

use App\Event\Trait\RestoredActivityEventTrait;

class RestoredNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RestoredActivityEventTrait;
}
