<?php

declare(strict_types=1);

namespace App\UserInterface\API\Representation;

use App\Entity\NewsletterRegistration;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Sulu\Bundle\SecurityBundle\Entity\User;

#[ExclusionPolicy(ExclusionPolicy::ALL)]
class NewsletterRegistrationRepresentation
{
    public function __construct(
        private readonly NewsletterRegistration $registration,
        private readonly ?User $user,
    ) {
    }

    #[VirtualProperty]
    #[SerializedName('id')]
    public function getId(): ?int
    {
        return $this->registration->getId();
    }

    #[VirtualProperty]
    #[SerializedName('locale')]
    public function getLocale(): string
    {
        return $this->registration->getLocale();
    }

    #[VirtualProperty]
    #[SerializedName('email')]
    public function getEmail(): string
    {
        return $this->registration->getEmail();
    }

    #[VirtualProperty]
    #[SerializedName('contact')]
    public function getContact(): ?int
    {
        return $this->user?->getContact()->getId();
    }
}
