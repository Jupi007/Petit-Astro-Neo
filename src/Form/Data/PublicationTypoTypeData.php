<?php

declare(strict_types=1);

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class PublicationTypoTypeData
{
    #[Assert\NotBlank]
    public string $description;
}
