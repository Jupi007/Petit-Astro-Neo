<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Sulu\Bundle\HeadlessBundle\Controller\HeadlessWebsiteController;
use Sulu\Bundle\WebsiteBundle\Controller\DefaultController;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

class BicephalWebsiteController extends AbstractController implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    #[SubscribedService]
    private function defaultController(): DefaultController
    {
        return $this->container->get(DefaultController::class);
    }

    #[SubscribedService]
    private function headlessController(): HeadlessWebsiteController
    {
        /* @return HeadlessWebsiteController */
        return $this->container->get(HeadlessWebsiteController::class);
    }

    public function indexAction(
        Request $request,
        StructureInterface $structure,
        bool $preview = false,
        bool $partial = false,
    ): Response {
        if ('json' === $request->getRequestFormat()) {
            return $this->headlessController()->indexAction($request, $structure, $preview, $partial);
        }

        return $this->defaultController()->indexAction($structure, $preview, $partial);
    }
}
