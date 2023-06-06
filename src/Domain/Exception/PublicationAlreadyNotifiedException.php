<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Sulu\Component\Rest\Exception\TranslationErrorMessageExceptionInterface;

class PublicationAlreadyNotifiedException extends \Exception implements TranslationErrorMessageExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Notifications for this publication has already been sent.');
    }

    public function getMessageTranslationKey(): string
    {
        return 'app.admin.publication_already_notified';
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
