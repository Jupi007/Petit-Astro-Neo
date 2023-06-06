<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class NullAssertionException extends \Exception
{
    public function __construct()
    {
        parent::__construct("This value shouldn't be null.");
    }
}
