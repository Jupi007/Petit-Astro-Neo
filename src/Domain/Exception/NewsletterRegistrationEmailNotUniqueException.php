<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Sulu\Component\Rest\Exception\TranslationErrorMessageExceptionInterface;

class NewsletterRegistrationEmailNotUniqueException extends \Exception implements TranslationErrorMessageExceptionInterface
{
    public function __construct(
        private readonly string $email,
    ) {
        parent::__construct(\sprintf('The email "%s" already exists in newsletter database!', $email));
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMessageTranslationKey(): string
    {
        return 'app.admin.newsletter_email_not_unique';
    }

    public function getMessageTranslationParameters(): array
    {
        return ['{email}' => $this->email];
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'email' => $this->email,
        ];
    }
}
