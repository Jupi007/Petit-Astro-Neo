<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\ContactRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
class ContactRequest implements PersistableEntityInterface, AuditableInterface
{
    use AuditableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'contact_requests';
    final public const RESOURCE_ICON = 'su-envelope';

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $object,
        #[ORM\Column(length: 255)]
        private string $email,
        #[ORM\Column(type: Types::TEXT)]
        private string $message,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
