<?php

declare(strict_types=1);

namespace App\Entity\Contract;

use Sulu\Component\Persistence\Model\TimestampableInterface as SuluTimestampableInterface;

interface TimestampableInterface extends SuluTimestampableInterface
{
    public function setCreated(\DateTime $created): self;

    public function setChanged(\DateTime $changed): self;
}
