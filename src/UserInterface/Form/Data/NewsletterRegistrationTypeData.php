<?php

declare(strict_types=1);

namespace App\UserInterface\Form\Data;

use App\UserInterface\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class NewsletterRegistrationTypeData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[AppAssert\UniqueNewsletterEmail]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Locale]
    public string $locale;
}
