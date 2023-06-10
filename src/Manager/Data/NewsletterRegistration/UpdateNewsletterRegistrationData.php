<?php

declare(strict_types=1);

namespace App\Manager\Data\NewsletterRegistration;

class UpdateNewsletterRegistrationData
{
    public function __construct(
        public readonly int $id,
        public readonly string $locale,
    ) {
    }
}
