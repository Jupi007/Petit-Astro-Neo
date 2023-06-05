<?php

declare(strict_types=1);

namespace App\Common;

use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

trait DefaultLocaleGetterTrait
{
    private readonly WebspaceManagerInterface $webspaceManager;

    private function getDefaultLocale(): string
    {
        foreach ($this->webspaceManager->getAllLocalizations() as $localization) {
            if ($localization->isDefault()) {
                return $localization->getLocale(Localization::DASH);
            }
        }

        throw new \LogicException('No default locale.');
    }
}
