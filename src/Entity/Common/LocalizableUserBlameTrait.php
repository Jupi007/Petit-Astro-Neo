<?php

declare(strict_types=1);

namespace App\Entity\Common;

use App\Entity\Contract\UserBlameInterface;
use Sulu\Component\Security\Authentication\UserInterface;

/** @template T of UserBlameInterface */
trait LocalizableUserBlameTrait
{
    public function getCreator(): ?UserInterface
    {
        return $this->getTranslation()->getCreator();
    }

    public function setCreator(?UserInterface $creator): self
    {
        $this->getTranslation()->setCreator($creator);

        return $this;
    }

    public function getChanger(): ?UserInterface
    {
        return $this->getTranslation()->getChanger();
    }

    public function setChanger(?UserInterface $changer): self
    {
        $this->getTranslation()->setChanger($changer);

        return $this;
    }

    /** @return T */
    abstract private function getTranslation();
}
