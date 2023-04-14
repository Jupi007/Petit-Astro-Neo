<?php

declare(strict_types=1);

namespace App\Form\Data;

use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class NewsletterRegistrationTypeData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[AppAssert\UniqueNewsletterEmail]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Locale]
    public ?string $locale = null;
}
