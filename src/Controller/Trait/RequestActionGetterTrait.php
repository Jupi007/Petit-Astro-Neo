<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Symfony\Component\HttpFoundation\Request;

trait RequestActionGetterTrait
{
    public function getRequestAction(Request $request): ?string
    {
        return $request->query->get(
            key: 'action',
            default: null,
        );
    }
}
