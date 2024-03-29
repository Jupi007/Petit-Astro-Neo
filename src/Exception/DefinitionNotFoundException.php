<?php

declare(strict_types=1);

namespace App\Exception;

use Sulu\Component\Rest\Exception\TranslationErrorMessageExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_NOT_FOUND)]
class DefinitionNotFoundException extends \Exception implements TranslationErrorMessageExceptionInterface
{
    public function __construct()
    {
        parent::__construct('The definition does not exist or no longer exists.');
    }

    public function getMessageTranslationKey(): string
    {
        return 'app.admin.definition_not_found';
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
