<?php

declare(strict_types=1);

namespace App\Tests\Application\Entity\Common;

use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\PersistableEntityInterface;

class PersistableEntityTraitImplementation implements PersistableEntityInterface
{
    use PersistableEntityTrait;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
