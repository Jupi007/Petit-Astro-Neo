<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\NewsletterRegistrationRepository;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsletterRegistrationRepository::class)]
class NewsletterRegistration implements PersistableEntityInterface, AuditableInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'newsletter_registrations';
    final public const RESOURCE_ICON = 'su-envelope';

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

    public static function fromUser(User $user): self
    {
        if (null === $user->getEmail()) {
            throw new \LogicException('You cannot use an user without an email address.');
        }

        $registration = new self(
            $user->getEmail(),
            $user->getLocale(),
        );

        return $registration;
    }

    public function syncWithUser(User $user): self
    {
        if ($this->email !== $user->getEmail()) {
            throw new \LogicException(\sprintf(
                'You cannot sync with a different user email address (this: %s - user: %s).',
                $this->email,
                $user->getEmail(),
            ));
        }

        $this->setLocale($user->getLocale());

        return $this;
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
