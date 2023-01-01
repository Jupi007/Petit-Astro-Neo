<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Definition;
use App\Repository\DefinitionRepository;
use Symfony\Component\HttpFoundation\Request;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
    ) {
    }

    public function createFromRequest(Request $request): Definition
    {
        $definition = $this->updateFromRequest(new Definition(), $request);

        return $definition;
    }

    public function updateFromRequest(Definition $definition, Request $request): Definition
    {
        $data = $request->toArray();
        $locale = $request->query->get('locale');

        $definition->setLocale($locale ?? '');
        $definition->setTitle($data['title']);
        $definition->setContent($data['content']);

        $this->repository->save($definition);

        return $definition;
    }
}
