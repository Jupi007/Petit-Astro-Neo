<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Sulu\Bundle\HeadlessBundle\Content\StructureResolverInterface;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\PageInterface;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\Form\FormInterface;
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
     * @param mixed[] $attributes
     */
    protected function abstractIndexAction(
        Request $request,
        StructureInterface $structure,
        bool $preview = false,
        bool $partial = false,
        array $attributes = [],
    ): Response {
        if ('json' !== $request->getRequestFormat()) {
            $response = $this->renderStructure(
                $structure,
                $attributes,
                $preview,
                $partial,
            );

            if (Response::HTTP_OK === $response->getStatusCode()) {
                foreach ($attributes as $v) {
                    if ($v instanceof FormInterface && $v->isSubmitted() && !$v->isValid()) {
                        $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        break;
                    }
                }
            }

            return $response;
        }

        $headlessData = $this->resolveStructure($structure);
        $response = $this->json($headlessData);
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
}
