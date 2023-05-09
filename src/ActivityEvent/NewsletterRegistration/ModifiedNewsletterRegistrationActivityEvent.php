<?php

declare(strict_types=1);

namespace App\ActivityEvent\NewsletterRegistration;

use App\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use ModifiedActivityEventTrait;
}
