<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Sulu\Bundle\HeadlessBundle\Content\StructureResolverInterface;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\PageInterface;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * More convenient headless controller class to extend.
 * Mostly copied from Sulu\Bundle\HeadlessBundle\Controller\HeadlessWebsiteController.
 */
class AbstractHeadlessWebsiteController extends WebsiteController
{
    public function __construct(
        private readonly StructureResolverInterface $structureResolver,
    ) {
    }

    /**
     * We cannot set the typehint of the $structure parameter to PageInterface because the ArticleBundle does not
     * implement that interface. Therefore we need to define the type via phpdoc to satisfy phpstan.
     *
     * @param PageInterface $structure
     * @param mixed[] $structureAttributes
     */
    protected function abstractIndexAction(
        Request $request,
        StructureInterface $structure,
        bool $preview = false,
        bool $partial = false,
        array $structureAttributes = [],
    ): Response {
        if ('json' !== $request->getRequestFormat()) {
            return $this->renderStructure(
                $structure,
                $structureAttributes,
                $preview,
                $partial,
            );
        }

        $headlessData = $this->resolveStructure($structure);
        $response = new Response($this->serializeData($headlessData));
        $response->headers->set('Content-Type', 'application/json');

        $cacheLifetimeEnhancer = $this->getCacheTimeLifeEnhancer();
        if (!$preview && $cacheLifetimeEnhancer) {
            $cacheLifetimeEnhancer->enhance($response, $structure);
        }

        return $response;
    }

    /** @return mixed[] */
    private function resolveStructure(StructureInterface $structure): array
    {
        return $this->structureResolver->resolve($structure, $structure->getLanguageCode());
    }

    /** @param mixed[] $data */
    private function serializeData(array $data): string
    {
        return \json_encode($data, \JSON_THROW_ON_ERROR);
    }
}
