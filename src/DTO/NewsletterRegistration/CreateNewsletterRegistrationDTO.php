<?php

declare(strict_types=1);

namespace App\DTO\NewsletterRegistration;

class CreateNewsletterRegistrationDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $locale,
    ) {
    }
}
