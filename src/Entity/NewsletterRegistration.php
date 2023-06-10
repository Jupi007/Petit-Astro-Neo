<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Common\PersistableEntityTrait;
use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TrashableEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class NewsletterRegistration implements PersistableEntityInterface, AuditableInterface, TrashableEntityInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'newsletter_registrations';
    final public const RESOURCE_ICON = 'su-bell';

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $uuid;

    public function __construct(
        #[Assert\Unique]
        #[Assert\Email]
        #[ORM\Column(length: 255, unique: true)]
        private string $email,
        #[Assert\Locale]
        #[ORM\Column(length: 5)]
        private string $locale,
    ) {
        $this->uuid = Uuid::v4();
    }

    public static function getResourceKey(): string
    {
        return self::RESOURCE_KEY;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
