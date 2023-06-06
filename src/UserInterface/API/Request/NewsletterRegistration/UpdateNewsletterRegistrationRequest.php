<?php

declare(strict_types=1);

namespace App\UserInterface\API\Request\NewsletterRegistration;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateNewsletterRegistrationRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Locale]
        #[Assert\Length(max: 5)]
        public readonly string $locale,
    ) {
    }
}
