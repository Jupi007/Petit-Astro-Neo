<?php

declare(strict_types=1);

namespace App\Entity\Common;

use Sulu\Component\Security\Authentication\UserInterface;

trait UserBlameTrait
{
    protected ?UserInterface $creator;

    protected ?UserInterface $changer;

    public function getCreator(): ?UserInterface
    {
        return $this->creator;
    }

    public function setCreator(?UserInterface $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getChanger(): ?UserInterface
    {
        return $this->changer;
    }

    public function setChanger(?UserInterface $changer): self
    {
        $this->changer = $changer;

        return $this;
    }
}
