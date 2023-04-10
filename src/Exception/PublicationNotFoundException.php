<?php

declare(strict_types=1);

namespace App\Exception;

use Sulu\Component\Rest\Exception\TranslationErrorMessageExceptionInterface;

class PublicationNotFoundException extends \Exception implements TranslationErrorMessageExceptionInterface
{
    public function __construct()
    {
        parent::__construct('The publication does not exist or no longer exists.');
    }

    public function getMessageTranslationKey(): string
    {
        return 'app.admin.publication_not_found';
    }

    public function getMessageTranslationParameters(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
