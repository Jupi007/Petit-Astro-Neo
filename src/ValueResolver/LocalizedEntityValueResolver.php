<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\Contract\LocalizableInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class LocalizedEntityValueResolver implements ValueResolverInterface
{
    private readonly EntityValueResolver $entityValueResolver;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityValueResolver = new EntityValueResolver($registry);
    }

    /** @return LocalizableInterface[] */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->isVariadic() || !\is_subclass_of($argument->getType() ?? '', LocalizableInterface::class)) {
            return [];
        }

        if (!\is_string($locale = $request->query->get('locale'))) {
            return [];
        }

        $object = $this->entityValueResolver->resolve($request, $argument);

        if (1 !== \count($object)) {
            return [];
        }

        $object = $object[0];

        if (!$object instanceof LocalizableInterface) {
            return [];
        }

        return [$object->setLocale($locale)];
    }
}
