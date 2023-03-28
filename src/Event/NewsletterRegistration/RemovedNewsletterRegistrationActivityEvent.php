<?php

declare(strict_types=1);

namespace App\Event\NewsletterRegistration;

use App\Event\Trait\RemovedActivityEventTrait;

class RemovedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RemovedActivityEventTrait;
}
