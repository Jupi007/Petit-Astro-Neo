<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller\Common;

use App\Controller\Common\RequestActionGetterTrait;
use Symfony\Component\HttpFoundation\Request;

class RequestActionGetterTraitImplementation
{
    use RequestActionGetterTrait;

    public function getRequestActionForTesting(Request $request): ?string
    {
        return $this->getRequestAction($request);
    }
}
