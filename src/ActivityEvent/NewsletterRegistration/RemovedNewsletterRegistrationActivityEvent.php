<?php

declare(strict_types=1);

namespace App\ActivityEvent\NewsletterRegistration;

use App\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RemovedActivityEventTrait;
}
