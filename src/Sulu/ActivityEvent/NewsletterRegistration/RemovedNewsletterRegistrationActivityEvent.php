<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\NewsletterRegistration;

use App\Sulu\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RemovedActivityEventTrait;
}
