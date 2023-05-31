<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Security;

use Sulu\Component\Security\Authorization\MaskConverter as SuluMaskConverter;
use Sulu\Component\Security\Authorization\PermissionTypes as SuluPermissionTypes;

// Add custom permission types to MaskConverter
class MaskConverter extends SuluMaskConverter
{
    public function __construct()
    {
        parent::__construct([
            // Permission types from Sulu/Bundle/SecurityBundle/Resources/config/services.xml
            SuluPermissionTypes::SECURITY => 1,
            SuluPermissionTypes::LIVE => 2,
            SuluPermissionTypes::ARCHIVE => 4,
            SuluPermissionTypes::DELETE => 8,
            SuluPermissionTypes::EDIT => 16,
            SuluPermissionTypes::ADD => 32,
            SuluPermissionTypes::VIEW => 64,

            // Custom permission types.
            PermissionTypes::NOTIFY => 128,
        ]);
    }

    /** @return array<string, int> */
    public function convertPermissionsToArray($permissions): array
    {
        return [
            ...parent::convertPermissionsToArray($permissions),
            PermissionTypes::NOTIFY => (bool) ($permissions & $this->permissions[PermissionTypes::NOTIFY]),
        ];
    }
}
