<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\NewsletterRegistration;

use App\Infrastructure\Sulu\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use ModifiedActivityEventTrait;
}
