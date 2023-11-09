<?php

declare(strict_types=1);

namespace App\Entity\Common;

trait AuditableTrait
{
    use TimestampableTrait;
    use UserBlameTrait;
}
