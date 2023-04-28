<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\LocalizableInterface;
use App\Entity\Contract\PersistableEntityInterface;
use App\Entity\Contract\TrashableEntityInterface;
use App\Entity\Trait\LocalizableTrait;
use App\Entity\Trait\PersistableEntityTrait;
use App\Repository\DefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\RouteBundle\Model\RoutableInterface;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Security\Authentication\UserInterface;

#[ORM\Entity(repositoryClass: DefinitionRepository::class)]
class Definition implements PersistableEntityInterface, LocalizableInterface, RoutableInterface, TrashableEntityInterface
{
    /** @use LocalizableTrait<DefinitionTranslation> */
    use LocalizableTrait;
    use PersistableEntityTrait;

    final public const RESOURCE_KEY = 'definitions';
    final public const RESOURCE_ICON = 'fa-book';

    /** @var Collection<string, DefinitionTranslation> */
    #[ORM\OneToMany(
        targetEntity: DefinitionTranslation::class,
        mappedBy: 'definition',
        cascade: ['persist'],
        indexBy: 'locale',
        orphanRemoval: true,
    )]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public static function getResourceKey(): string
    {
        return self::RESOURCE_KEY;
    }

    private function createTranslation(): DefinitionTranslation
    {
        return new DefinitionTranslation($this, $this->locale);
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

    public function getDescription(): ?string
    {
        return $this->getTranslation()?->getDescription();
    }

    public function setDescription(string $description): self
    {
        $this->getTranslation(createIfNull: true)->setDescription($description);

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

    public function getRoutePath(): ?string
    {
        return $this->getTranslation()?->getRoutePath();
    }

    public function setRoutePath(string $route): self
    {
        $this->getTranslation(createIfNull: true)->setRoutePath($route);

        return $this;
    }
}
