<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestQueryValueResolver implements ValueResolverInterface
{
    /**
     * @return string[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        return !$argument->isVariadic() && $request->query->has($argument->getName()) ? [$request->query->get($argument->getName())] : [];
    }
}
