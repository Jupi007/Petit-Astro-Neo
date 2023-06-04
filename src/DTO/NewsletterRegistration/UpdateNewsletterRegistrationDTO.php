<?php

declare(strict_types=1);

namespace App\DTO\NewsletterRegistration;

class UpdateNewsletterRegistrationDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $locale,
    ) {
    }
}
