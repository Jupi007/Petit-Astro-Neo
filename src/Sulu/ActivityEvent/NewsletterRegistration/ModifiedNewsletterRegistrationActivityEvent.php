<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\NewsletterRegistration;

use App\Sulu\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use ModifiedActivityEventTrait;
}
