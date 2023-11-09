<?php

declare(strict_types=1);

namespace App\Entity\Common;

trait TimestampableTrait
{
    protected \DateTime $created;

    protected \DateTime $changed;

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getChanged(): \DateTime
    {
        return $this->changed;
    }

    public function setChanged(\DateTime $changed): self
    {
        $this->changed = $changed;

        return $this;
    }
}
