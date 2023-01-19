<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\LocalizableInterface;
use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Trait\LocalizableTrait;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\RouteBundle\Model\RoutableInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Security\Authentication\UserInterface;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication implements PersistableEntityInterface, LocalizableInterface, RoutableInterface
{
    /** @use LocalizableTrait<PublicationTranslation> */
    use LocalizableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'publications';
    final public const RESOURCE_ICON = 'su-news';

    /** @var Collection<string, PublicationTranslation> */
    #[ORM\OneToMany(
        targetEntity: PublicationTranslation::class,
        mappedBy: 'publication',
        cascade: ['persist'],
        indexBy: 'locale',
        orphanRemoval: true,
    )]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    private function createTranslation(): PublicationTranslation
    {
        return new PublicationTranslation($this, $this->locale);
    }

    public function getTitle(): ?string
    {
        return $this->getTranslation()?->getTitle();
    }

    public function setTitle(string $title): self
    {
        $this->getTranslation(createIfNull: true)->setTitle($title);

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->getTranslation()?->getSubtitle();
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->getTranslation(createIfNull: true)->setSubtitle($subtitle);

        return $this;
    }

    /** @return mixed[] */
    public function getContent(): ?array
    {
        return $this->getTranslation()?->getContent();
    }

    /** @param mixed[] $content */
    public function setContent(array $content): self
    {
        $this->getTranslation(createIfNull: true)->setContent($content);

        return $this;
    }

    public function getCreator(): ?UserInterface
    {
        return $this->getTranslation()?->getCreator();
    }

    public function setCreator(?UserInterface $creator): self
    {
        $this->getTranslation(createIfNull: true)->setCreator($creator);

        return $this;
    }

    public function getChanger(): ?UserInterface
    {
        return $this->getTranslation()?->getChanger();
    }

    public function setChanger(?UserInterface $changer): self
    {
        $this->getTranslation(createIfNull: true)->setChanger($changer);

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->getTranslation()?->getCreated();
    }

    public function setCreated(\DateTime $created): self
    {
        $this->getTranslation(createIfNull: true)->setCreated($created);

        return $this;
    }

    public function getChanged(): ?\DateTime
    {
        return $this->getTranslation()?->getChanged();
    }

    public function setChanged(\DateTime $changed): self
    {
        $this->getTranslation(createIfNull: true)->setChanged($changed);

        return $this;
    }

    public function getRoute(): ?RouteInterface
    {
        return $this->getTranslation()?->getRoute();
    }

    public function setRoute(RouteInterface $route): self
    {
        $this->getTranslation(createIfNull: true)->setRoute($route);

        return $this;
    }
}
