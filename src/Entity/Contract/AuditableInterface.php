<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface AuditableInterface extends TimestampableInterface, UserBlameInterface
{
}
