<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Definition;
use Symfony\Component\HttpFoundation\Request;

class DefinitionFactory
{
    public static function createFromRequest(Request $request): Definition
    {
        return self::updateFromRequest(new Definition(), $request);
    }

    public static function updateFromRequest(Definition $definition, Request $request): Definition
    {
        $data = $request->toArray();
        $locale = $request->query->get('locale');

        $definition->setLocale($locale ?? '');
        $definition->setTitle($data['title'] ?? '');
        $definition->setContent($data['content'] ?? '');

        return $definition;
    }
}
