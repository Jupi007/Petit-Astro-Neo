<?php

declare(strict_types=1);

namespace App\UserInterface\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueNewsletterEmail extends Constraint
{
    public string $message = 'validators.unique_newsletter_email';

    /** @param mixed[] $options */
    public function __construct(array $options = null, string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
