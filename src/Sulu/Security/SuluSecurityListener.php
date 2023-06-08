<?php

declare(strict_types=1);

namespace App\Sulu\Security;

use Sulu\Component\Security\Authorization\PermissionTypes as SuluPermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Security\Authorization\SecurityCondition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Listens on the kernel.controller event and checks if Sulu allows this action.
 * Modified version of Sulu\Bundle\SecurityBundle\EventListener\SuluSecurityListener,
 * taking into account custom permission types.
 */
class SuluSecurityListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * Checks if the action is allowed for the current user, and throws an Exception otherwise.
     *
     * @throws AccessDeniedException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        $controllerAction = null;

        if (\is_array($controller)) {
            if (isset($controller[1])) {
                $controllerAction = $controller[1];
            }

            if (isset($controller[0])) {
                $controller = $controller[0];
            }
        }

        if (
            !$controller instanceof SecuredControllerInterface
            && !$controller instanceof SecuredObjectControllerInterface
        ) {
            return;
        }

        $request = $event->getRequest();
        $action = $controller->getRequestAction($request);

        // find appropriate permission type for request
        $permission = '';

        switch ($request->getMethod()) {
            case 'GET':
                $permission = SuluPermissionTypes::VIEW;
                break;
            case 'POST':
                if ('postAction' === $controllerAction) {
                    $permission = SuluPermissionTypes::ADD;
                } elseif ('notify' === $action) {
                    $permission = PermissionTypes::NOTIFY;
                } else {
                    $permission = SuluPermissionTypes::EDIT;
                }
                break;
            case 'PUT':
            case 'PATCH':
                $permission = SuluPermissionTypes::EDIT;
                break;
            case 'DELETE':
                $permission = SuluPermissionTypes::DELETE;
                break;
        }

        $securityContext = null;
        $locale = $controller->getLocale($request);
        $objectType = null;
        $objectId = null;

        if ($controller instanceof SecuredObjectControllerInterface) {
            $objectType = $controller->getSecuredClass();
            $objectId = $controller->getSecuredObjectId($request);
        }

        // check permission
        if ($controller instanceof SecuredControllerInterface) {
            $securityContext = $controller->getSecurityContext();
        }

        if (null !== $securityContext) {
            $this->securityChecker->checkPermission(
                new SecurityCondition($securityContext, $locale, $objectType, $objectId),
                $permission,
            );
        }
    }
}
