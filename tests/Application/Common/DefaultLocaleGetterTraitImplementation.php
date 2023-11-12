<?php

declare(strict_types=1);

namespace App\Tests\Application\Common;

use App\Common\DefaultLocaleGetterTrait;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class DefaultLocaleGetterTraitImplementation
{
    use DefaultLocaleGetterTrait;

    public function __construct(
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
    }

    public function getDefaultLocaleForTesting(): ?string
    {
        return $this->getDefaultLocale();
    }
}
