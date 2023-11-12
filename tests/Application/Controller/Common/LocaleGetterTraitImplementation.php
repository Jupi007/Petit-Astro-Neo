<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller\Common;

use App\Controller\Common\LocaleGetterTrait;
use Symfony\Component\HttpFoundation\Request;

class LocaleGetterTraitImplementation
{
    use LocaleGetterTrait;

    public function getLocaleForTesting(Request $request): string
    {
        return $this->getLocale($request);
    }
}
