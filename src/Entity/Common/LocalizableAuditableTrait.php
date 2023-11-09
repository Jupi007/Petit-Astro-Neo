<?php

declare(strict_types=1);

namespace App\Entity\Common;

use App\Entity\Contract\TimestampableInterface;
use App\Entity\Contract\UserBlameInterface;

/** @template T of UserBlameInterface&TimestampableInterface */
trait LocalizableAuditableTrait
{
    /** @use LocalizableTimestampableTrait<T> */
    use LocalizableTimestampableTrait;
    /** @use LocalizableUserBlameTrait<T> */
    use LocalizableUserBlameTrait;
}
