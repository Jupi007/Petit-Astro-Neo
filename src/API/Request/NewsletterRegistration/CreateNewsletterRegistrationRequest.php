<?php

declare(strict_types=1);

namespace App\API\Request\NewsletterRegistration;

use Symfony\Component\Validator\Constraints as Assert;

class CreateNewsletterRegistrationRequest
{
    public function __construct(
        public readonly ?int $contact = null,
        // ------------------
        #[Assert\When(
            expression: 'this.contact === null',
            constraints: [
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Length(max: 255),
            ],
        )]
        public readonly ?string $email = null,
        // ------------------
        #[Assert\When(
            expression: 'this.contact === null',
            constraints: [
                new Assert\NotBlank(),
                new Assert\Locale(),
                new Assert\Length(max: 5),
            ],
        )]
        public readonly ?string $locale = null,
    ) {
    }
}
