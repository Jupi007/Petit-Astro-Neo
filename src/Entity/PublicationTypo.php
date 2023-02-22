<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublicationTypoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationTypoRepository::class)]
class PublicationTypo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'typos')]
        #[ORM\JoinColumn(nullable: false)]
        private Publication $publication,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublication(): Publication
    {
        return $this->publication;
    }
}
