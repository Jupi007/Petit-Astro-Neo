<?php

declare(strict_types=1);

namespace App\Manager\Data\NewsletterRegistration;

class CreateNewsletterRegistrationData
{
    public function __construct(
        public readonly string $email,
        public readonly string $locale,
    ) {
    }
}
