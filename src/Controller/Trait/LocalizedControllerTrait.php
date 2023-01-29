<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Symfony\Component\HttpFoundation\Request;

trait LocalizedControllerTrait
{
    public function getLocale(Request $request): ?string
    {
        return $request->query->get(
            key: 'locale',
            default: null,
        );
    }
}
