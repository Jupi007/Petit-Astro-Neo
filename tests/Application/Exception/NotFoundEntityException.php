<?php

declare(strict_types=1);

namespace App\Tests\Application\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_NOT_FOUND)]
class NotFoundEntityException extends \Exception
{
    public function __construct()
    {
        parent::__construct('The entity does not exist or no longer exists.');
    }
}
