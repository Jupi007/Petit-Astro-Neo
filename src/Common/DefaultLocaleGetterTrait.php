<?php

declare(strict_types=1);

namespace App\Common;

use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

trait DefaultLocaleGetterTrait
{
    private function getDefaultLocale(): string
    {
        foreach ($this->getWebspaceManager()->getAllLocalizations() as $localization) {
            if ($localization->isDefault()) {
                return $localization->getLocale(Localization::DASH);
            }
        }

        throw new \LogicException('No default locale.');
    }

    abstract protected function getWebspaceManager(): WebspaceManagerInterface;
}
