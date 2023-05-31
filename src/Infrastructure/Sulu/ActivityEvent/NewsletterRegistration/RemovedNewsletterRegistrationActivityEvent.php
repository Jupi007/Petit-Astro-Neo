<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\NewsletterRegistration;

use App\Infrastructure\Sulu\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use RemovedActivityEventTrait;
}
