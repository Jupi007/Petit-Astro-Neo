<?php

declare(strict_types=1);

namespace App\Tests\Application\Entity;

use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\PersistableEntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TestEntity implements PersistableEntityInterface
{
    use PersistableEntityTrait;

    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
        private string $title = 'title',
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
