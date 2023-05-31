<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controllers implementing this interface security will be automatically secured.
 * Modified version of Sulu\Component\Security\SecuredControllerInterface.
 */
interface SecuredControllerInterface
{
    /**
     * Returns the SecurityContext required for the controller.
     */
    public function getSecurityContext(): string;

    /**
     * Returns the locale for the given request.
     */
    public function getLocale(Request $request): ?string;

    /**
     * Returns the action for the given request.
     */
    public function getRequestAction(Request $request): ?string;
}
