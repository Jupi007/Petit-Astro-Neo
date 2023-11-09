<?php

declare(strict_types=1);

namespace App\Entity\Contract;

use Sulu\Component\Persistence\Model\UserBlameInterface as SuluUserBlameInterface;
use Sulu\Component\Security\Authentication\UserInterface;

interface UserBlameInterface extends SuluUserBlameInterface
{
    public function setCreator(?UserInterface $creator): self;

    public function setChanger(?UserInterface $changer): self;
}
