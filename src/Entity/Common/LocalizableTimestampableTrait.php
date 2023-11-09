<?php

declare(strict_types=1);

namespace App\Entity\Common;

use App\Entity\Contract\TimestampableInterface;

/** @template T of TimestampableInterface */
trait LocalizableTimestampableTrait
{
    public function getCreated(): ?\DateTime
    {
        return $this->getTranslation()->getCreated();
    }

    public function setCreated(\DateTime $created): self
    {
        $this->getTranslation()->setCreated($created);

        return $this;
    }

    public function getChanged(): ?\DateTime
    {
        return $this->getTranslation()->getChanged();
    }

    public function setChanged(\DateTime $changed): self
    {
        $this->getTranslation()->setChanged($changed);

        return $this;
    }

    /** @return T */
    abstract private function getTranslation();
}
