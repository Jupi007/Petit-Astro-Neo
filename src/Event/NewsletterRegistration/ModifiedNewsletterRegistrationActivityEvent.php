<?php

declare(strict_types=1);

namespace App\Event\NewsletterRegistration;

use App\Event\Trait\ModifiedActivityEventTrait;

class ModifiedNewsletterRegistrationActivityEvent extends AbstractNewsletterRegistrationActivityEvent
{
    use ModifiedActivityEventTrait;
}
