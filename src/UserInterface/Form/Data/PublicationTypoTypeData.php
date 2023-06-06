<?php

declare(strict_types=1);

namespace App\UserInterface\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class PublicationTypoTypeData
{
    #[Assert\NotBlank]
    public string $description;
}
